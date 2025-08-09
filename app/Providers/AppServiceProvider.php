<?php

namespace App\Providers;

use App\Socialite\SsoProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

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
        $socialite = $this->app->make(Factory::class);
        
        $socialite->extend('sso', function ($app) use ($socialite) {
            $config = $app['config']['services.sso'];
            return $socialite->buildProvider(SsoProvider::class, $config);
        });
    }
}
