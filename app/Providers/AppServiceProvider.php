<?php

namespace App\Providers;

use App\CallBusyMessage;
use App\DatabaseLog;
use App\Observers\CallBusyMessageObserver;
use App\ScrapedProducts;
use Blade;
use Facebook\Facebook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Studio\Totem\Totem;
use Illuminate\Pagination\Paginator;

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
