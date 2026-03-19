<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Fix 5: Missing Rate Limiting on Authentication
        RateLimiter::for('login', function (Request $request) {
            return [
                // IP-based: 10 attempts per minute
                Limit::perMinute(10)->by($request->ip()),
                // Email-based: 5 attempts per 15 minutes (using a separate limiter or handling in controller)
            ];
        });
    }
}
