<?php

use App\Http\Controllers\Api\DevotionalController;
use App\Http\Controllers\Api\MemoryVerseController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MFACustomController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('v1/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::post('v1/login', [LoginController::class, 'login']);
Route::post('v1/register', [RegisterController::class, 'register']);
Route::post('v1/verify-registration-otp', [OtpController::class, 'verifyRegistrationOtp']);
Route::post('v1/verify-login-otp', [OtpController::class, 'verifyLoginOtp']);
Route::post('v1/resend-otp', [OtpController::class, 'resendOtp']);
Route::post('v1/send-reset-otp', [ResetPasswordController::class, 'sendResetOtp']);
Route::post('v1/reset-password', [ResetPasswordController::class, 'reset']);

// memory verses and devotional routes protected by API token
Route::middleware('api.token')->group(function () {
    // memory verses routes
    Route::get('v1/memories', [MemoryVerseController::class, 'list']);
    Route::get('v1/memories/{id}/details', [MemoryVerseController::class, 'details']);

    // devotional routes
    Route::get('v1/devotionals', [DevotionalController::class, 'index']);
    Route::get('v1/devotionals/today', [DevotionalController::class, 'today']);
    Route::get('v1/devotionals/upcoming', [DevotionalController::class, 'upcoming']);
});

// social auth routes
Route::post('/auth/google', [SocialAuthController::class, 'googleAuth']);
Route::post('/auth/google/callback', [SocialAuthController::class, 'googleCallback']);

Route::middleware(['auth:sanctum', 'api.encrypt'])->group(function () {
    Route::get('v1/mfa/setup', [MFACustomController::class, 'setup']);
    Route::post('v1/mfa/activate', [MFACustomController::class, 'activate']);
    Route::post('v1/mfa/verify', [MFACustomController::class, 'verify']);
});

Route::middleware(['auth:sanctum', 'api.encrypt'])->prefix('v1/payment')->group(function () {
    Route::get('/plans', [PaymentController::class, 'getSubscriptionPlans']);
    Route::post('/verify-receipt', [SubscriptionController::class, 'verifyReceipt']);
    Route::post('/create-subscription', [PaymentController::class, 'createSubscription']);
    Route::post('/create-support', [PaymentController::class, 'createSupport']);
    Route::post('/confirm-recurring-support', [PaymentController::class, 'confirmRecurringSupport']);
    Route::post('/cancel-recurring-support', [PaymentController::class, 'cancelRecurringSupport']);
    Route::post('/create-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
    Route::get('/status', [PaymentController::class, 'checkPaymentStatus']);
    Route::get('/recurring-support-plans', [PaymentController::class, 'getRecurringSupportPlans']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('v1/devotional/{id}/details', [DevotionalController::class, 'getDetails']);
    // profile routes
    Route::get('v1/profile', [ProfileController::class, 'profile']);
    // note routes
    Route::get('v1/notes', [NoteController::class, 'index']);
    Route::get('v1/notes/{id}/show', [NoteController::class, 'show']);
    Route::put('v1/notes/{id}/update', [NoteController::class, 'update']);
    Route::post('v1/notes/store', [NoteController::class, 'store']);
    Route::delete('v1/notes/{id}/delete', [NoteController::class, 'destroy']);
    // prayer routes
    Route::get('v1/prayers', [App\Http\Controllers\Api\PrayerController::class, 'index']);
    Route::post('v1/prayers/store', [App\Http\Controllers\Api\PrayerController::class, 'store']);
    Route::put('v1/prayers/{prayerNote}/update', [App\Http\Controllers\Api\PrayerController::class, 'update']);
    Route::get('v1/prayers/{prayerNote}/show', [App\Http\Controllers\Api\PrayerController::class, 'show']);
    Route::delete('v1/prayers/{prayerNote}/delete', [App\Http\Controllers\Api\PrayerController::class, 'delete']);
    // profile update routes
    Route::put('v1/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('v1/profile/change-image', [ProfileController::class, 'changeProfileImage']);
    Route::post('v1/profile/change-password', [ProfileController::class, 'changePassword']);
});

Route::get('/user', function (Request $request) {

    return $request->user();
})->middleware('auth:sanctum');
