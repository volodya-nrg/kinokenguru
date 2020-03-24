<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MediafireProvider extends ServiceProvider
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
        $this->app->bind('App\MyClasses\Mediafire', function () {
            return new Mediafire();
        });
    }
}
