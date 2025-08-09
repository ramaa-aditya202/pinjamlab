<?php
// filepath: app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Socialite\SsoProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

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
        // Register custom SSO OAuth2 provider
        Socialite::extend('sso', function ($app) {
            $config = config('services.sso');
            return Socialite::buildProvider(SsoProvider::class, $config);
        });
    }
}