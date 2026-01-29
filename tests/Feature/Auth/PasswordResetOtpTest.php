<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOtpMail;

it('sends OTP email on password reset request', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'testuser@example.com',
    ]);

    $response = $this->postJson('/api/reset-password', [
        'email' => $user->email,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'OTP sent to your email.']);

    Mail::assertSent(PasswordResetOtpMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && is_numeric($mail->otp);
    });
});
