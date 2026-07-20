<?php

use App\Models\User;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

it('sends OTP email on password reset request', function () {
    Mail::fake();
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'phone' => '08012345678',
    ]);

    $response = $this->postJson('/api/v1/send-reset-otp', [
        'email' => $user->email,
        'cf-turnstile-response' => 'test-token',
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'OTP sent to email']);

    Mail::assertSent(PasswordResetOtpMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
