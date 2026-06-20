<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\AuthOtpMail;
use App\Traits\SendsSmsOtp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use SendsSmsOtp;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'username' => 'nullable|string|max:255',
            'password' => ['required', 'string', 'confirmed','min:8'],
            'cf-turnstile-response' => ['required', 'turnstile'],
            'referral_code' => ['nullable', 'string'],
        ]);

        try {
            // account exist check
            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json(['errors' => 'Account already exists'], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ];

            if ($request->filled('referral_code')) {
                $referralCode = ReferralCode::where('code', $request->referral_code)->first();
                
                if (!$referralCode || !$referralCode->isValid()) {
                    return response()->json(['errors' => 'Invalid or expired referral code.'], 422);
                }

                $referralCode->increment('uses_count');

                $userData['referral_code_id'] = $referralCode->id;
                $userData['has_paid'] = true;
                $userData['payment_expires_at'] = null;
            }

            $user = User::create($userData);

            // Generate OTP
            $otp = rand(100000, 999999);
            $cacheKey = 'registration_otp_' . strtolower($request->email);
            Cache::put($cacheKey, $otp, now()->addMinutes(10));

            // Send OTP via Termii SMS
            $this->sendOtpWithTermii($user->phone, $otp);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your account with the OTP sent to your phone.',
                'otp_required' => true,
                'email' => $request->email
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'errors' => 'Registration failed. Please try again.'], 500);
        }
    }
}
