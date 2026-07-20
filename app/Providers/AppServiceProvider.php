<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('turnstile', function ($attribute, $value, $parameters, $validator) {
            if (empty($value)) return false;

            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            return $response->json('success');
        });

        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            if (empty($value)) return false;

            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            return $response->json('success');
        });

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
