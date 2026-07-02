<?php

use App\Models\User;
use App\Mail\AuthOtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);
});

it('registers a user and sends Email OTP', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'phone' => '08098765432',
        'username' => 'johndoe',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'cf-turnstile-response' => 'test-token',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Registration successful. Please verify your account with the OTP sent to your email.',
        'otp_required' => true,
        'email' => 'johndoe@example.com'
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'johndoe@example.com',
        'phone' => '08098765432',
    ]);

    Mail::assertSent(AuthOtpMail::class, function ($mail) {
        return $mail->hasTo('johndoe@example.com') && str_contains($mail->message_text, 'registration OTP');
    });
});

it('logs in a user and sends Email OTP', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'phone' => '09011112222',
        'password' => bcrypt('Password123!'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'user@example.com',
        'password' => 'Password123!',
        'cf-turnstile-response' => 'test-token',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Credentials verified. Please enter the OTP sent to your email.',
        'otp_required' => true,
        'email' => 'user@example.com'
    ]);

    Mail::assertSent(AuthOtpMail::class, function ($mail) {
        return $mail->hasTo('user@example.com');
    });
});

it('verifies registration OTP', function () {
    $user = User::factory()->create([
        'email' => 'verify@example.com',
        'phone' => '08122334455',
        'email_verified_at' => null,
    ]);

    Cache::put('registration_otp_verify@example.com', '123456', now()->addMinutes(10));

    $response = $this->postJson('/api/v1/verify-registration-otp', [
        'email' => 'verify@example.com',
        'otp' => '123456',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Email verified successfully.',
    ]);

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
});

it('verifies login OTP', function () {
    $user = User::factory()->create([
        'email' => 'login-verify@example.com',
        'phone' => '08122334466',
    ]);

    Cache::put('login_otp_login-verify@example.com', '654321', now()->addMinutes(10));

    $response = $this->postJson('/api/v1/verify-login-otp', [
        'email' => 'login-verify@example.com',
        'otp' => '654321',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Login successful.',
    ]);
});

it('resends OTP via Email', function () {
    $user = User::factory()->create([
        'email' => 'resend@example.com',
        'phone' => '08122334477',
    ]);

    $response = $this->postJson('/api/v1/resend-otp', [
        'email' => 'resend@example.com',
        'type' => 'login',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'OTP has been resent to your email.',
    ]);

    Mail::assertSent(AuthOtpMail::class, function ($mail) {
        return $mail->hasTo('resend@example.com') && str_contains($mail->message_text, 'login OTP');
    });
});
