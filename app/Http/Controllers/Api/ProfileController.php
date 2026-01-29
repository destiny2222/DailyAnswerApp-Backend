<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            // $user = User::find($user->id);
            return response()->json([
                'success' => true,
                'data' => new ProfileResource($user),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'An error occurred while fetching profile data.'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            $user->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new ProfileResource($user),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => 'An error occurred while updating profile data.',
            ], 500);
        }
    }

    public function changeProfileImage(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'profile_image' => 'required|image|max:2048',
            ]);

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('profile'), $imageName);

                // Update user's profile image URL
                $user->profile_photo_url = $imageName;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile image updated successfully.',
                'data' => new ProfileResource($user),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'An error occurred while changing profile image.'], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => 'Current password is incorrect.',
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'An error occurred while changing password.'], 500);
        }
    }

    
}
