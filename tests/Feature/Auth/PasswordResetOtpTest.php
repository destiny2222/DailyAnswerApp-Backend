<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

it('sends OTP SMS via Termii on password reset request', function () {
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        'https://v3.api.termii.com/*' => Http::response(['message_id' => '123456'], 200),
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
    $response->assertJson(['message' => 'OTP sent to phone']);

    Http::assertSent(function ($request) use ($user) {
        return str_contains($request->url(), 'api.termii.com/api/sms/send') &&
               $request['to'] === '+2348012345678' &&
               str_contains($request['sms'], 'OTP');
    });
});
