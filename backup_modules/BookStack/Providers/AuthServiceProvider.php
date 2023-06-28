<?php

namespace Modules\BookStack\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Modules\BookStack\Auth\Access\LdapService;

class AuthServiceProvider extends ServiceProvider
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
        Auth::provider('ldap', function ($app, array $config) {
            return new LdapUserProvider($config['model'], $app[LdapService::class]);
        });
    }
}
