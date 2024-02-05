<?php

namespace App\Providers;

use Blade;
use App\DatabaseLog;
use Facebook\Facebook;
use Studio\Totem\Totem;
use App\CallBusyMessage;
use App\ScrapedProducts;
use Illuminate\View\View;
use Illuminate\Support\Facades;
use App\Models\GoogleDocsCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Observers\CallBusyMessageObserver;
use App\User;
use App\StoreWebsite;
use App\MagentoCommand;
use App\AssetsManager;
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
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
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
         
        view()->composer('*',function($view) {

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
