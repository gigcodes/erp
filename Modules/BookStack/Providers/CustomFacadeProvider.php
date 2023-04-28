<?php

namespace Modules\BookStack\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BookStack\Actions\ViewService;
use Modules\BookStack\Uploads\ImageService;
use Modules\BookStack\Actions\ActivityService;
use Modules\BookStackModules\BookStack\Settings\SettingService;

class CustomFacadeProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('activity', function () {
            return $this->app->make(ActivityService::class);
        });

        $this->app->bind('views', function () {
            return $this->app->make(ViewService::class);
        });

        $this->app->bind('setting', function () {
            return $this->app->make(SettingService::class);
        });

        $this->app->bind('images', function () {
            return $this->app->make(ImageService::class);
        });
    }
}
