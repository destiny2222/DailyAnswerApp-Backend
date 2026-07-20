<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http; // Added this line
use Illuminate\Support\Str;
use App\Mail\PasswordResetOtpMail;
use App\Mail\AuthOtpMail;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'cf-turnstile-response' => ['required', 'turnstile'],
        ];

        if ($request->email === 'testuser@gmail.com') {
            unset($rules['cf-turnstile-response']);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        $failCountKey = 'login_fails:' . $throttleKey;

        // Fix 6: CAPTCHA / Bot Detection (Trigger after 3 fails)
        $fails = \Illuminate\Support\Facades\Cache::get($failCountKey, 0);
        if ($fails >= 3) {
            $captchaValidated = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'turnstile_token' => 'required|string',
            ]);

            if ($captchaValidated->fails()) {
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
            
            return response()->json([
                'errors' => ["Too many login attempts. Please try again in $seconds seconds."],
                'lockout_seconds' => $seconds
            ], 429);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 900); // 15 minutes lockout after 5 hits
            \Illuminate\Support\Facades\Cache::increment($failCountKey, 1);
            \Illuminate\Support\Facades\Cache::put($failCountKey, \Illuminate\Support\Facades\Cache::get($failCountKey), 900);

            if (RateLimiter::attempts($throttleKey) === 5) {
                // Send lockout email
                Mail::raw("Your account has been locked for 15 minutes due to multiple failed login attempts.", function ($message) use ($request) {
                    $message->to($request->email)->subject("Security Alert: Account Locked");
                });
            }

            return response()->json(['errors' => ['Invalid email address or password.']], 401);
        }

        RateLimiter::clear($throttleKey);

        // Fix 7: Multi-Factor Authentication (MFA) Check
        if ($user->google2fa_secret) {
            return response()->json([
                'success' => true,
                'mfa_required' => true,
                'message' => 'MFA verification required.'
            ], 200);
        }

        // too much attempt check
        $tooMuchAttempt = Cache::get('too_much_attempt_'.$request->email);
        if ($tooMuchAttempt) {
            return response()->json(['errors' => 'Too many attempts. Please try again later.'], 422);
        }

        // Bypass OTP for demo credentials
        if ($request->email === 'testuser@gmail.com' && $request->password === 'Test@1234') {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'user' => $user
            ], 200);
        }

        // Generate Login OTP
        $otp = rand(100000, 999999);
        $cacheKey = 'login_otp_'.strtolower($request->email);
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Send OTP via Email
        $appName = config('app.name', 'Daily Answer');
        Mail::to($user->email)->send(new AuthOtpMail($otp, "Your {$appName} login OTP is: {$otp}. Valid for 10 minutes. Do not share."));

        return response()->json([
            'success' => true,
            'message' => 'Credentials verified. Please enter the OTP sent to your email.',
            'otp_required' => true,
            'email' => $request->email
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
