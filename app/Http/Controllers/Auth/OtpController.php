<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthOtpMail;

class OtpController extends Controller
{

    public function verifyRegistrationOtp(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cacheKey = 'registration_otp_'.strtolower($request->email);
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['errors' => ['otp' => ['Invalid or expired OTP.']]], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['errors' => ['email' => ['User not found.']]], 404);
        }

        $user->email_verified_at = now();
        $user->save();

        // Forget OTP
        Cache::forget($cacheKey);

        // Generate token
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function verifyLoginOtp(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cacheKey = 'login_otp_'.strtolower($request->email);
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['errors' => ['otp' => ['Invalid or expired OTP.']]], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['errors' => ['email' => ['User not found.']]], 404);
        }

        // Forget OTP
        Cache::forget($cacheKey);

        // Revoke old tokens if you want single session or just create new one
        // $user->tokens()->delete(); 

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function resendOtp(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'type' => 'required|string|in:registration,login',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['errors' => ['email' => ['User not found.']]], 404);
        }

        $otp = rand(100000, 999999);
        $cacheKey = $request->type . '_otp_' . strtolower($request->email);
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        $appName = config('app.name', 'Daily Answer');
        $message = $request->type == 'registration' 
            ? "Your {$appName} registration OTP is: {$otp}. Valid for 10 minutes. Do not share."
            : "Your {$appName} login OTP is: {$otp}. Valid for 10 minutes. Do not share.";

        // Send OTP via Email
        Mail::to($user->email)->send(new AuthOtpMail($otp, $message));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been resent to your email.'
        ], 200);
    }
}
