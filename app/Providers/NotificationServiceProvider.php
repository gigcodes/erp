<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Composers\NotificationComposer;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('partials.notifications', NotificationComposer::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
