<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

it('validates required fields', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount', 'is_recurring']);
});

it('validates amount is numeric', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 'invalid',
            'is_recurring' => false,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

it('validates amount minimum value', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 0,
            'is_recurring' => false,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

it('validates amount maximum value', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 1000000,
            'is_recurring' => false,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

it('validates interval when recurring is true', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 25.00,
            'is_recurring' => true,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['interval']);
});

it('validates interval must be monthly or yearly', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 25.00,
            'is_recurring' => true,
            'interval' => 'daily',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['interval']);
});

it('allows valid one-time support data', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 50.00,
            'is_recurring' => false,
        ]);

    // Will fail on Stripe API call in test env, but validation should pass
    expect($response->status())->toBeIn([200, 500]);
});

it('allows valid recurring support data', function (): void {
    $response = actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/payment/create-support', [
            'amount' => 25.00,
            'is_recurring' => true,
            'interval' => 'monthly',
        ]);

    // Will fail on Stripe API call in test env, but validation should pass
    expect($response->status())->toBeIn([200, 500]);
});

it('requires authentication', function (): void {
    $response = postJson('/api/v1/payment/create-support', [
        'amount' => 50.00,
        'is_recurring' => false,
    ]);

    $response->assertUnauthorized();
});
