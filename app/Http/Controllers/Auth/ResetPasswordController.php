<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Traits\SendsSmsOtp;

class ResetPasswordController extends Controller
{
    use SendsSmsOtp;

    public function sendResetOtp(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'cf-turnstile-response' => ['required', 'turnstile'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'errors' => ['email' => ['User not found']],
                'message' => 'User not found',
            ], 404);
        }

        if (empty($user->phone)) {
            return response()->json(['errors' => ['phone' => ['A phone number is required on this account to receive OTP. Please contact support.']]], 422);
        }

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        // Store OTP in cache for 10 minutes
        $cacheKey = 'password_reset_otp_'.strtolower($email);
        Cache::put($cacheKey, $otp, now()->addMinutes(10));
        
        // Send OTP via Termii SMS
        $this->sendOtpWithTermii($user->phone, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to phone',
        ], 200);
    }

    public function reset(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');

        $user = User::where('email', $email)->first();
        if (! $user) {
            return response()->json([
                'errors' => 'User not found',
            ], 404);
        }

        $cacheKey = 'password_reset_otp_'.strtolower($email);
        $expectedOtp = Cache::get($cacheKey);

        if (! $expectedOtp || ! hash_equals((string) $expectedOtp, (string) $request->input('otp'))) {
            return response()->json(['errors' => 'Invalid or expired OTP.'], 422);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Invalidate OTP
        Cache::forget($cacheKey);

        // Revoke existing API tokens
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return response()->json(['success' => true, 'message' => 'Password reset successful'], 200);
    }
}
