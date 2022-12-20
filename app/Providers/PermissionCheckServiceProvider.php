<?php

namespace App\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class PermissionCheckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(ViewFactory $view)
    {
        $view->composer('*', \App\Http\Composers\GlobalComposer::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('permissioncheck', function () {
            return new \App\Helpers\PermissionCheck;
        });
    }
}
