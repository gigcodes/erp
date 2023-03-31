<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Setting;
use Schema;
use Config;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(Schema::hasTable('settings')){
            $setting = Setting::where('name', 'telescope_enabled')->first();
        }
        
        /* Example 1: Set Config Value in Laravel */
        Config::set('telescope.enabled',  (!empty($setting) && $setting->val == 1 ? true : false));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}