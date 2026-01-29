<?php

use App\Models\Admin;
use App\Models\SubscriptionPlan;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    $role = Role::create(['name' => 'super-admin', 'guard_name' => 'admin']);
    $this->admin->assignRole($role);

    $role->givePermissionTo([
        'subscription.view',
        'create subscription',
        'edit subscription',
        'delete subscription',
    ]);
});

it('displays subscription plans index page', function () {
    $plans = SubscriptionPlan::factory()->count(3)->create();

    $response = actingAs($this->admin, 'admin')
        ->get(route('admin.subscription.index'));

    $response->assertSuccessful()
        ->assertViewIs('admin.subscription.index')
        ->assertViewHas('subscriptions');
});

it('displays the create subscription plan form', function () {
    $response = actingAs($this->admin, 'admin')
        ->get(route('admin.subscription.create'));

    $response->assertSuccessful()
        ->assertViewIs('admin.subscription.create');
});

it('can create a new subscription plan', function () {
    $planData = [
        'name' => 'Premium Monthly',
        'price' => 19.99,
        'interval' => 'monthly',
        'plan_id' => 'plan_premium_monthly',
        'slug' => 'premium-monthly',
        'features' => [
            'Unlimited access',
            'Priority support',
            'No ads',
        ],
    ];

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), $planData);

    $response->assertRedirect(route('admin.subscription.index'))
        ->assertSessionHas('success', 'Subscription Plan Created Successfully');

    assertDatabaseHas('subscription_plans', [
        'name' => 'Premium Monthly',
        'price' => 19.99,
        'interval' => 'monthly',
        'plan_id' => 'plan_premium_monthly',
        'slug' => 'premium-monthly',
    ]);
});

it('validates required fields when creating a subscription plan', function () {
    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), []);

    $response->assertSessionHasErrors(['name', 'price', 'interval', 'plan_id']);
});

it('validates unique plan_id when creating a subscription plan', function () {
    SubscriptionPlan::factory()->create(['plan_id' => 'plan_existing']);

    $planData = [
        'name' => 'New Plan',
        'price' => 9.99,
        'interval' => 'monthly',
        'plan_id' => 'plan_existing',
        'slug' => 'new-plan',
    ];

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), $planData);

    $response->assertSessionHasErrors(['plan_id']);
});

it('validates unique slug when creating a subscription plan', function () {
    SubscriptionPlan::factory()->create(['slug' => 'existing-slug']);

    $planData = [
        'name' => 'New Plan',
        'price' => 9.99,
        'interval' => 'monthly',
        'plan_id' => 'plan_new',
        'slug' => 'existing-slug',
    ];

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), $planData);

    $response->assertSessionHasErrors(['slug']);
});

it('displays the edit subscription plan form', function () {
    $plan = SubscriptionPlan::factory()->create();

    $response = actingAs($this->admin, 'admin')
        ->get(route('admin.subscription.edit', $plan->id));

    $response->assertSuccessful()
        ->assertViewIs('admin.subscription.edit')
        ->assertViewHas('subscription', $plan);
});

it('can update an existing subscription plan', function () {
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Old Name',
        'price' => 10.00,
    ]);

    $updateData = [
        'name' => 'Updated Name',
        'price' => 15.99,
        'interval' => $plan->interval,
        'plan_id' => $plan->plan_id,
        'slug' => $plan->slug,
        'features' => ['Updated feature 1', 'Updated feature 2'],
    ];

    $response = actingAs($this->admin, 'admin')
        ->put(route('admin.subscription.update', $plan->id), $updateData);

    $response->assertRedirect(route('admin.subscription.index'))
        ->assertSessionHas('success', 'Subscription Plan Updated Successfully');

    assertDatabaseHas('subscription_plans', [
        'id' => $plan->id,
        'name' => 'Updated Name',
        'price' => 15.99,
    ]);
});

it('validates required fields when updating a subscription plan', function () {
    $plan = SubscriptionPlan::factory()->create();

    $response = actingAs($this->admin, 'admin')
        ->put(route('admin.subscription.update', $plan->id), []);

    $response->assertSessionHasErrors(['name', 'price', 'interval', 'plan_id']);
});

it('can delete a subscription plan', function () {
    $plan = SubscriptionPlan::factory()->create();

    $response = actingAs($this->admin, 'admin')
        ->delete(route('admin.subscription.delete', $plan->id));

    $response->assertRedirect(route('admin.subscription.index'))
        ->assertSessionHas('success', 'Subscription Plan Deleted Successfully');

    assertDatabaseMissing('subscription_plans', [
        'id' => $plan->id,
    ]);
});

it('stores features as json array', function () {
    $planData = [
        'name' => 'Test Plan',
        'price' => 9.99,
        'interval' => 'monthly',
        'plan_id' => 'plan_test',
        'slug' => 'test-plan',
        'features' => [
            'Feature 1',
            'Feature 2',
            'Feature 3',
        ],
    ];

    actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), $planData);

    $plan = SubscriptionPlan::where('plan_id', 'plan_test')->first();

    expect($plan->features)
        ->toBeArray()
        ->toHaveCount(3)
        ->toContain('Feature 1');
});

it('allows creating a plan without features', function () {
    $planData = [
        'name' => 'Basic Plan',
        'price' => 0.00,
        'interval' => 'monthly',
        'plan_id' => 'plan_basic',
        'slug' => 'basic',
        'features' => null,
    ];

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.subscription.store'), $planData);

    $response->assertRedirect(route('admin.subscription.index'));

    assertDatabaseHas('subscription_plans', [
        'plan_id' => 'plan_basic',
    ]);
});
