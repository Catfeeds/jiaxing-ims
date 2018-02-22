<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;

use App\EventDispatcher;
use App\Database\MySqlConnection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });
        
        $this->app->singleton('dispatcher', function ($app, $deps) {
            return new EventDispatcher();
        });
    }
}
