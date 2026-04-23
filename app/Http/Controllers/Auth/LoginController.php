<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'cf-turnstile-response' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // increment attempt count
            $attemptCount = Cache::get('attempt_count_'.$request->email, 0);
            $attemptCount++;
            Cache::put('attempt_count_'.$request->email, $attemptCount, now()->addMinutes(15));
            if ($attemptCount >= 5) {
                Cache::put('too_much_attempt_'.$request->email, true, now()->addMinutes(15));
            }
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }

        // too much attempt check
        $tooMuchAttempt = Cache::get('too_much_attempt_'.$request->email);
        if ($tooMuchAttempt) {
            return response()->json(['errors' => 'Too many attempts. Please try again later.'], 422);
        }

        // Generate Login OTP
        $otp = rand(100000, 999999);
        $cacheKey = 'login_otp_'.strtolower($request->email);
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Send OTP
        Mail::to($request->email)->send(new AuthOtpMail($otp, 'Use the code below to complete your login.'));

        return response()->json([
            'success' => true,
            'message' => 'Credentials verified. Please enter the OTP sent to your email.',
            'otp_required' => true,
            'email' => $request->email
        ], 200);
    }
}
