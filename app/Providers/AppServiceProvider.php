<?php

namespace App\Providers;

use App\CallBusyMessage;
use App\Models\GoogleDocsCategory;
use App\Observers\CallBusyMessageObserver;
use App\ScrapedProducts;
use Blade;
use Facebook\Facebook;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Studio\Totem\Totem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        Totem::auth(function ($request) {
            // return true / false . For e.g.
            return \Auth::check();
        });

        if (in_array(app('request')->ip(), config('debugip.ips'))) {
            config(['app.debug' => true]);
            config(['debugbar.enabled' => true]);
        }

        Validator::extend('valid_base', function ($attribute, $value, $parameters, $validator) {
            if (base64_decode($value, true) !== false) {
                return true;
            } else {
                return false;
            }
        }, 'image is not valid base64 encoded string.');

//         DB::listen(function ($query) {
//             if($query->time>2000){
//                 Log::channel("server_audit")->info("time exceeded 2000: ".$query->time, ["url"=>request()->url(),"sql"=>$query->sql,$query->bindings]);
//                 DatabaseLog::create(['url' =>request()->url(), 'sql_data' => $query->sql, 'time_taken' => $query->time,'log_message' =>json_encode($query->bindings)]);
//             }
//         });

        CallBusyMessage::observe(CallBusyMessageObserver::class);

        Paginator::useBootstrap();

        Facades\View::composer(['googledocs.index', 'development.flagtask', 'development.issue', 'task-module.show', 'task-module.*'], function (View $view) {
            $googledocscategory = GoogleDocsCategory::get()->pluck('name', 'id')->toArray();
            if (count($googledocscategory) > 0) {
                $view->with('googleDocCategory', $googledocscategory);
            } else {
                $view->with('googleDocCategory', []);
            }
        });


        /**
         * @param mixed|null      $data
         * @param mixed           $message
         * @param \Throwable|null $e
         * @param int             $code
         * @param array           $headers
         *
         * @return mixed|null
         */
        Response::macro(name: 'jsonResponse', macro: function (mixed $message = '',bool $success = true, mixed $data = null, \Throwable $e = null, int $code = Response::HTTP_OK, $statusMessages = [], array $headers = []) {
            $response = [];
            $response['success'] = $success;

            if ($data) {
                $response['data'] = $data;
            }
            if ($message) {
                $response['message'] = $message;
            }
            if (count($statusMessages)) {
                $response['status_messages'] = $statusMessages;
            }
            if ($e && config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTrace(),
                ];
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json($response, $code, $headers);
        });
    }

    /**
     * Register any applicxation services.
     *
     * @return void
     */
    public function register()
    {
        //
//        if(!env('CI')) {
        $this->app->singleton(Facebook::class, function ($app) {
            return new Facebook(config('facebook.config'));
        });
//        }

        $this->app->singleton(ScrapedProducts::class);
    }
}
