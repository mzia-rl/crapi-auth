<?php

namespace Canzell\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

use Canzell\Auth\Guards\CRAPIGuard;
use Canzell\Extensions\CRAPIUserProvider;

class CRAPIAuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Register Config File
        $this->publishes([
            __DIR__.'/../../config/crapi-auth.php' => config_path('crapi-auth.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../../config/crapi-auth.php', 'crapi-auth'
        );

        // Register User Provider Driver
        Auth::provider('crapi', function ($app, array $config) {
            return new CRAPIUserProvider($config);
        });

        // Register Auth Driver
        Auth::extend('crapi', function ($app, $name, array $config) {
            return new CRAPIGuard(
                Auth::createUserProvider($config['provider'] ?? 'crapi'),
                request()->bearerToken()
            );
        });
    }

}