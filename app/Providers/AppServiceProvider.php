<?php

namespace App\Providers;

use Blade;
use Facebook\Facebook;
use Studio\Totem\Totem;
use App\CallBusyMessage;
use App\ScrapedProducts;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades;
use App\Models\GoogleDocsCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Observers\CallBusyMessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Force assets to ssl
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
        //
        Schema::defaultStringLength(191);

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        Totem::auth(function ($request) {
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

        CallBusyMessage::observe(CallBusyMessageObserver::class);

        Paginator::useBootstrap();
        $google_docs_category = GoogleDocsCategory::get()->pluck('name', 'id')->toArray();
        Facades\View::composer(['googledocs.index', 'development.flagtask', 'development.issue', 'task-module.show', 'task-module.*'], function (View $view) use ($google_docs_category) {
            if (count($google_docs_category) > 0) {
                $view->with('googleDocCategory', $google_docs_category);
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
        Response::macro(name: 'jsonResponse', macro: function (mixed $message = '', bool $success = true, mixed $data = null, \Throwable $e = null, int $code = Response::HTTP_OK, $statusMessages = [], array $headers = []) {
            $response            = [];
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

        Builder::macro('whereLike', function ($columns, $search) {
            $this->where(function ($query) use ($columns, $search) {
                foreach (\Arr::wrap($columns) as $column) {
                    $query->orWhere($column, $search);
                }
            });

            return $this;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Facebook::class, function ($app) {
            return new Facebook(config('facebook.config'));
        });
        $this->app->singleton(ScrapedProducts::class);
    }
}
