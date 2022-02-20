<?php

namespace AleeDhillon\MetaFive;

use Illuminate\Support\ServiceProvider;

class MetaFiveProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/meta-five.php' => config_path('meta-five.php'),
        ], 'meta-five-config');

        $this->app->singleton('meta-five', function ($app) {

            $client = new Client();

            $client->connect();

            return $client;
        });
    }
}
