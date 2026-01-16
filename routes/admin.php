<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;



Route::prefix('admin')->name('admin.')->group(function (){
    Route::controller(LoginController::class)->group(function (){
        Route::get('login-form','showLoginForm')->name('login-Form');
        Route::post('login','login')->name('login');
        Route::post('logout','logout')->name('logout');
    });

    Route::middleware(['adminLogged'])->group(function () {
        Route::get('/dashboard', [ HomeController::class,'home' ])->name('home');

    });


    // customer
    Route::get('/users', [HomeController::class, 'customer'])->name('customer.index');
    Route::get('/customer/{id}/edit', [HomeController::class, 'EditCustomer'])->name('customer.edit');
    Route::put('/customer/{id}/update', [HomeController::class, 'UpdateCustomer'])->name('customer-update');
    Route::delete('/customer/{id}/delete', [HomeController::class, 'deleteCustomer'])->name('customer-delete');

    // Profile and password routes
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile-page');
    Route::put('/profile_update/{id}', [HomeController::class, 'update'])->name('update-profile-page');
    Route::put('change-password', [HomeController::class, 'validatepassword'])->name('change-password-page');

    Route::get('optimize',function (){
        \Illuminate\Support\Facades\Artisan::call('optimize');
        return 1;
    });
    Route::get('clear',function (){
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return 1;
    });
});
