<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Cache;

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

        // too much attempt check
        $tooMuchAttempt = Cache::get('too_much_attempt_'.$request->email);
        if ($tooMuchAttempt) {
            return response()->json(['errors' => 'Too many attempts. Please try again later.'], 422);
        }

        // Authentication logic
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = Auth::user()->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
            ], 200);
        } else {
            // increment attempt count
            $attemptCount = Cache::get('attempt_count_'.$request->email, 0);
            $attemptCount++;
            Cache::put('attempt_count_'.$request->email, $attemptCount, now()->addMinutes(15));
            if ($attemptCount >= 5) {
                Cache::put('too_much_attempt_'.$request->email, true, now()->addMinutes(15));
            }
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }
    }
}
