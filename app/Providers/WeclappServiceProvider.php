<?php

namespace App\Providers;

use App\Services\WeclappService;
use Illuminate\Support\ServiceProvider;

class WeclappServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WeclappService::class, function ($app) {
            return new WeclappService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 