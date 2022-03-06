<?php

namespace App\Providers;

use App\Services\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard($app->request);
        });
    }
}
