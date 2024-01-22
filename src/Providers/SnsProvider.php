<?php

namespace Canzell\Aws\Sns\Providers;

use Illuminate\Support\ServiceProvider;
use Canzell\Aws\Sns\Client;
use Canzell\Aws\Sns\Middleware\{ ConfirmSubscription, ValidateNotification };

class SnsProvider extends ServiceProvider
{
    private $config;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Client::class,
            fn () => new Client($this->config)
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootConfig();
        $this->bootHttp();
    }

    private function bootConfig()
    {
        $this->publishes([
            __DIR__.'/../../config/aws.php' => config_path('aws.php')
        ], 'crapi-sns-config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/aws.php', 'aws'
        );

        $this->config = config('aws.sns');
    }

    private function bootHttp()
    {
        $router = app('router');
        $router->middlewareGroup('subscriptions', [
            ValidateNotification::class,
            ConfirmSubscription::class
        ]);
        $this->loadRoutesFrom(__DIR__.'/../../routes/subscriptions.php');
    }

}
