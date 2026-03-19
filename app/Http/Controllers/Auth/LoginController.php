<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http; // Added this line
use Illuminate\Support\Str;
use App\Mail\PasswordResetOtpMail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        $failCountKey = 'login_fails:' . $throttleKey;

        // Fix 6: CAPTCHA / Bot Detection (Trigger after 3 fails)
        $fails = \Illuminate\Support\Facades\Cache::get($failCountKey, 0);
        if ($fails >= 3) {
            $validated = Validator::make($request->all(), [
                'turnstile_token' => 'required|string',
            ]);

            if ($validated->fails()) {
                return response()->json(['errors' => ['CAPTCHA verification required.'], 'captcha_required' => true], 422);
            }

            // Validate Turnstile Token
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret') ?? env('TURNSTILE_SECRET_KEY'),
                'response' => $request->turnstile_token,
                'remoteip' => $request->ip(),
            ]);

            if (!$response->json('success')) {
                return response()->json(['errors' => ['CAPTCHA verification failed.']], 422);
            }
        }

        // Fix 2 & 5: Account Lockout & Rate Limiting
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Logic for escalating lockout (simplified for this context)
            // If already hit 5 attempts, lockout for 15 mins (900s)
            // Laravel default is usually per minute, but we can customize.
            
            return response()->json([
                'errors' => ["Too many login attempts. Please try again in $seconds seconds."],
                'lockout_seconds' => $seconds
            ], 429);
        }

        // Authentication logic
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            $user = Auth::user();

            // Fix 7: Multi-Factor Authentication (MFA) Check
            if ($user->google2fa_secret) {
                // If MFA is enabled, don't return the full token yet
                // Instead, return a temporary token or just the requirement
                return response()->json([
                    'success' => true,
                    'mfa_required' => true,
                    'message' => 'MFA verification required.'
                ], 200);
            }
            
            $token = $user->createToken('authToken')->plainTextToken;

            // Fix 1: Rotate tokens on login
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id ?? null)->delete();

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            RateLimiter::hit($throttleKey, 900); // 15 minutes lockout after 5 hits
            \Illuminate\Support\Facades\Cache::increment($failCountKey, 1);
            \Illuminate\Support\Facades\Cache::put($failCountKey, \Illuminate\Support\Facades\Cache::get($failCountKey), 900);

            if (RateLimiter::attempts($throttleKey) === 5) {
                // Send lockout email
                Mail::raw("Your account has been locked for 15 minutes due to multiple failed login attempts.", function ($message) use ($request) {
                    $message->to($request->email)->subject("Security Alert: Account Locked");
                });
            }

            // Fix 4: Verbose Authentication Error Messages
            return response()->json(['errors' => ['Invalid email address or password.']], 401);
        }
    }
}
