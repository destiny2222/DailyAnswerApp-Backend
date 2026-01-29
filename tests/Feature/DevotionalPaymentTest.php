<?php

use App\Models\Devotional;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create([
        'has_paid' => false,
    ]);

    $this->devotional = Devotional::create([
        'title' => 'Test Devotional',
        'content' => 'This is test content for the devotional.',
        'author' => 'Test Author',
        'key_verse' => 'John 3:16',
        'verses' => 'For God so loved the world...',
        'date' => now(),
        'status' => 'published',
        'published_at' => now(),
    ]);
});

it('prevents unpaid users from accessing devotional details', function () {
    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertForbidden()
        ->assertJson([
            'success' => false,
            'message' => 'Payment required to access full devotional details.',
            'requires_payment' => true,
        ]);
});

it('allows paid users to access devotional details', function () {
    $this->user->update(['has_paid' => true]);

    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'content',
            ],
        ]);
});

it('prevents unauthenticated users from accessing devotional details', function () {
    $response = getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertUnauthorized();
});

it('allows users with permanent payment to access details', function () {
    $this->user->update([
        'has_paid' => true,
        'payment_expires_at' => null,
    ]);

    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertSuccessful();
});

it('allows users with valid subscription to access details', function () {
    $this->user->update([
        'has_paid' => true,
        'payment_expires_at' => now()->addMonth(),
    ]);

    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertSuccessful();
});

it('prevents users with expired subscription from accessing details', function () {
    $this->user->update([
        'has_paid' => true,
        'payment_expires_at' => now()->subDay(),
    ]);

    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertForbidden();
});

it('returns 404 for non-existent devotional', function () {
    $this->user->update(['has_paid' => true]);

    $response = actingAs($this->user)
        ->getJson('/api/v1/devotional/99999/details');

    $response->assertStatus(500);
});

it('returns 500 for unpublished devotional even with payment', function () {
    $this->user->update(['has_paid' => true]);

    $this->devotional->update(['status' => 'draft']);

    $response = actingAs($this->user)
        ->getJson("/api/v1/devotional/{$this->devotional->id}/details");

    $response->assertStatus(500);
});
