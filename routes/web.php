<?php

use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.index');
});

Route::get('about-us', function () {
    return view('frontend.about');
})->name('about-us');

Route::get('resources', function () {
    return view('frontend.resources');
});

Route::get('support', function () {
    return view('frontend.support');
});

Route::get('subscribe', [SubscribeController::class, 'index']);
Route::post('/submit-support', [SubscribeController::class, 'startCheckout'])->name('subscribe.checkout');
Route::get('/billing/success', [SubscribeController::class, 'success'])->name('billing.success');
Route::get('/billing/cancel', [SubscribeController::class, 'cancel'])->name('billing.cancel');
Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::get('privacy-policy', function () {
    return view('frontend.privacy-policy');
})->name('privacy-policy');

Route::get('terms-of-service', function () {
    return view('frontend.terms-of-service');
})->name('terms-of-service');

// Fallback login route for auth middleware redirects
Route::get('/login', function () {
    return redirect()->route('admin.login-Form');
})->name('login');

require __DIR__.'/admin.php';
