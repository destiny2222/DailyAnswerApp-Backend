<?php

use App\Models\Admin;
use App\Models\Devotional;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = Admin::factory()->create();

    // Create and assign necessary permissions
    Permission::create(['name' => 'devotionals.view', 'guard_name' => 'admin']);
    Permission::create(['name' => 'devotionals.create', 'guard_name' => 'admin']);
    Permission::create(['name' => 'devotionals.edit', 'guard_name' => 'admin']);

    $this->admin->givePermissionTo(['devotionals.view', 'devotionals.create', 'devotionals.edit']);

    $this->actingAs($this->admin, 'admin');
});

test('admin can view bulk create form', function () {
    $response = $this->get(route('admin.devotionals.bulk-create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.devotionals.bulk-create');
});

test('admin can bulk create devotionals', function () {
    $devotionalsData = [
        'devotionals' => [
            [
                'title' => 'First Devotional',
                'content' => 'This is the first devotional content.',
                'date' => now()->format('Y-m-d'),
                'status' => 'draft',
            ],
            [
                'title' => 'Second Devotional',
                'content' => 'This is the second devotional content.',
                'date' => now()->addDay()->format('Y-m-d'),
                'status' => 'in_review',
            ],
        ],
    ];

    $response = $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $response->assertRedirect(route('admin.devotionals.index'));
    $response->assertSessionHas('success', 'Successfully created 2 devotionals.');

    expect(Devotional::count())->toBe(2);
    expect(Devotional::where('title', 'First Devotional')->exists())->toBeTrue();
    expect(Devotional::where('title', 'Second Devotional')->exists())->toBeTrue();
});

test('admin can bulk create devotionals with images', function () {
    if (! function_exists('imagecreatetruecolor')) {
        $this->markTestSkipped('GD extension is not installed.');
    }

    $image1 = UploadedFile::fake()->image('devotional1.jpg');
    $image2 = UploadedFile::fake()->image('devotional2.png');

    $devotionalsData = [
        'devotionals' => [
            [
                'title' => 'First Devotional',
                'content' => 'First content',
                'image' => $image1,
            ],
            [
                'title' => 'Second Devotional',
                'content' => 'Second content',
                'image' => $image2,
            ],
        ],
    ];

    $response = $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $response->assertRedirect(route('admin.devotionals.index'));

    $devotionals = Devotional::all();
    expect($devotionals)->toHaveCount(2);

    foreach ($devotionals as $devotional) {
        expect($devotional->image)->not->toBeNull();
        Storage::disk('public')->assertExists($devotional->image);
    }
});

test('bulk create requires at least title and content', function () {
    $devotionalsData = [
        'devotionals' => [
            [
                'title' => '',
                'content' => '',
            ],
        ],
    ];

    $response = $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['devotionals.0.title', 'devotionals.0.content']);
});

test('bulk create can handle optional fields', function () {
    $devotionalsData = [
        'devotionals' => [
            [
                'title' => 'Complete Devotional',
                'content' => 'Content here',
                'subheading' => 'Bible in a Year: Genesis 1-3',
                'key_verse' => 'John 3:16',
                'verses' => 'For God so loved the world...',
                'application_note' => 'Apply this truth to your life',
                'prayer_note' => 'Pray for understanding',
            ],
        ],
    ];

    $response = $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $response->assertRedirect(route('admin.devotionals.index'));

    $devotional = Devotional::first();
    expect($devotional->subheading)->toBe('Bible in a Year: Genesis 1-3');
    expect($devotional->key_verse)->toBe('John 3:16');
    expect($devotional->verses)->toBe('For God so loved the world...');
    expect($devotional->application_note)->toBe('Apply this truth to your life');
    expect($devotional->prayer_note)->toBe('Pray for understanding');
});

test('bulk create respects maximum limit of 10 devotionals', function () {
    $devotionalsData = ['devotionals' => []];

    for ($i = 1; $i <= 11; $i++) {
        $devotionalsData['devotionals'][] = [
            'title' => "Devotional {$i}",
            'content' => "Content {$i}",
        ];
    }

    $response = $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['devotionals']);
});

test('bulk create sets created_by to current admin', function () {
    $devotionalsData = [
        'devotionals' => [
            [
                'title' => 'Test Devotional',
                'content' => 'Test content',
            ],
        ],
    ];

    $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $devotional = Devotional::first();
    expect($devotional->created_by)->toBe($this->admin->id);
});

test('bulk create uses default date if not provided', function () {
    $devotionalsData = [
        'devotionals' => [
            [
                'title' => 'Test Devotional',
                'content' => 'Test content',
            ],
        ],
    ];

    $this->post(route('admin.devotionals.bulk-store'), $devotionalsData);

    $devotional = Devotional::first();
    expect($devotional->date->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});
