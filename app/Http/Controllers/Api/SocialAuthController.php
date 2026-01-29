<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
     /**
     * Handle Google authentication with access token from mobile app
     */
    public function googleAuth(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // Get user info from Google using the access token
            $googleUser = Socialite::driver('google')->userFromToken($request->access_token);

            // Find or create user
            $user = User::where('email', $googleUser->email)
                ->orWhere('google_id', $googleUser->id)
                ->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'has_paid' => $user->has_paid,
                    'payment_status' => $user->payment_status,
                ],
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Alternative: Handle callback URL (for web-based flow redirected to app)
     */
    public function googleCallback(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            // Exchange code for token
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create user (same logic as above)
            $user = User::where('email', $googleUser->email)
                ->orWhere('google_id', $googleUser->id)
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'has_paid' => $user->has_paid,
                    'payment_status' => $user->payment_status,
                ],
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
