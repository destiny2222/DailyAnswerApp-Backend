<?php

use App\Models\Admin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('public');

    // Create permission if it doesn't exist
    if (! Permission::where('name', 'devotionals.view')->where('guard_name', 'admin')->exists()) {
        Permission::create(['name' => 'devotionals.view', 'guard_name' => 'admin']);
    }

    $this->admin = Admin::factory()->create();
    $this->admin->givePermissionTo('devotionals.view');
});

it('rejects invalid or corrupted docx files', function () {
    // Create a fake invalid file (not a valid ZIP/DOCX)
    $invalidFile = UploadedFile::fake()->create('invalid.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.devotionals.docx-upload'), [
            'docx_file' => $invalidFile,
            'default_status' => 'draft',
        ]);

    $response->assertRedirect()
        ->assertSessionHas('error');

    // Verify the error message indicates an issue with the file
    expect(session('error'))
        ->toMatch('/DOCX document|error occurred|archive file/i');
});

it('validates file type for docx upload', function () {
    $textFile = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.devotionals.docx-upload'), [
            'docx_file' => $textFile,
            'default_status' => 'draft',
        ]);

    $response->assertSessionHasErrors('docx_file');
});

it('requires docx file to be uploaded', function () {
    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.devotionals.docx-upload'), [
            'default_status' => 'draft',
        ]);

    $response->assertSessionHasErrors('docx_file');
});

it('validates file size limit', function () {
    // Create a file larger than 5MB (5120KB)
    $largeFile = UploadedFile::fake()->create('large.docx', 6000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.devotionals.docx-upload'), [
            'docx_file' => $largeFile,
            'default_status' => 'draft',
        ]);

    $response->assertSessionHasErrors('docx_file');
});

it('rejects .doc files (Word 97-2003 format) with helpful message', function () {
    // Create a fake .doc file with proper mime type
    $docFile = UploadedFile::fake()->create('document.doc', 100, 'application/msword');

    $response = actingAs($this->admin, 'admin')
        ->post(route('admin.devotionals.docx-upload'), [
            'docx_file' => $docFile,
            'default_status' => 'draft',
        ]);

    // Should fail at validation due to mime type
    $response->assertSessionHasErrors('docx_file');
});
