<?php

use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DevotionalController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\MemoryVerseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubscriptionManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::controller(LoginController::class)->group(function () {
        Route::get('login-form', 'showLoginForm')->name('login-Form');
        Route::post('login', 'login')->name('post-login');
        Route::post('logout', 'logout')->name('logout');
    });

    Route::middleware(['auth:admin'])->group(function () {

        Route::get('/dashboard', [HomeController::class, 'home'])->name('home')->middleware('permission:dashboard.view,admin');

        Route::get('/profile', [HomeController::class, 'profile'])->name('profile-page');
        Route::put('/profile_update/{id}', [HomeController::class, 'update'])->name('update-profile-page');
        Route::put('change-password', [HomeController::class, 'validatepassword'])->name('change-password-page');

        // user management
        Route::prefix('users')->middleware('permission:users.view,admin')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('customer.index');
            Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('customer.edit');
            Route::delete('/{id}/delete', [UserManagementController::class, 'destroy'])->name('customer.delete');
        });

        // Roles management
        Route::prefix('role')->middleware('permission:roles.view,admin')->group(function () {
            Route::get('', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/{role}/update', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/{role}/delete', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Devotionals management
        Route::prefix('devotionals')->middleware('permission:devotionals.view,admin')->group(function () {
            Route::get('', [DevotionalController::class, 'index'])->name('devotionals.index');
            Route::get('/create', [DevotionalController::class, 'create'])->name('devotionals.create');
            Route::post('/store', [DevotionalController::class, 'store'])->name('devotionals.store');
            Route::get('/{devotional}', [DevotionalController::class, 'show'])->name('devotionals.show');
            Route::get('/{devotional}/edit', [DevotionalController::class, 'edit'])->name('devotionals.edit');
            Route::put('/{devotional}/update', [DevotionalController::class, 'update'])->name('devotionals.update');
            Route::delete('/{devotional}/delete', [DevotionalController::class, 'destroy'])->name('devotionals.destroy');
            Route::post('/{devotional}/publish', [DevotionalController::class, 'publish'])->name('devotionals.publish');
            Route::post('/{devotional}/unpublish', [DevotionalController::class, 'unpublish'])->name('devotionals.unpublish');
        });

        // Admin management
        Route::prefix('admins')->middleware('permission:admins.view,admin')->group(function () {
            Route::get('', [AdminManagementController::class, 'index'])->name('admins.index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('admins.create');
            Route::post('/store', [AdminManagementController::class, 'store'])->name('admins.store');
            Route::get('/{admin}', [AdminManagementController::class, 'show'])->name('admins.show');
            Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('admins.edit');
            Route::put('/{admin}/update', [AdminManagementController::class, 'update'])->name('admins.update');
            Route::delete('/{admin}/delete', [AdminManagementController::class, 'destroy'])->name('admins.destroy');
        });

        // Memory Verses management
        Route::prefix('memory-verses')->middleware('permission:devotionals.view,admin')->group(function () {
            Route::get('', [MemoryVerseController::class, 'index'])->name('memory_verses.index');
            Route::get('/create', [MemoryVerseController::class, 'create'])->name('memory_verses.create');
            Route::post('/store', [MemoryVerseController::class, 'store'])->name('memory_verses.store');
            Route::get('/{memoryVerse}', [MemoryVerseController::class, 'show'])->name('memory_verses.show');
            Route::get('/{memoryVerse}/edit', [MemoryVerseController::class, 'edit'])->name('memory_verses.edit');
            Route::put('/{memoryVerse}/update', [MemoryVerseController::class, 'update'])->name('memory_verses.update');
            Route::delete('/{memoryVerse}/delete', [MemoryVerseController::class, 'destroy'])->name('memory_verses.destroy');
        });

        // Subscription Management
        Route::prefix('subscription')->middleware('permission:subscription.view,admin')->group(function () {
            Route::get('/', [SubscriptionManagementController::class, 'index'])->name('subscription.index');
            Route::get('/create', [SubscriptionManagementController::class, 'create'])->name('subscription.create');
            Route::post('/store', [SubscriptionManagementController::class, 'store'])->name('subscription.store');
            Route::get('/{id}/edit', [SubscriptionManagementController::class, 'edit'])->name('subscription.edit');
            Route::put('/{id}/update', [SubscriptionManagementController::class, 'update'])->name('subscription.update');
            Route::delete('/{id}/delete', [SubscriptionManagementController::class, 'destroy'])->name('subscription.delete');
        });

    });
});
