<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Default string length in the database schema
        Schema::defaultStringLength(191);

        // Force URLS to be generated with https when on production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
