<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
     return redirect()->route('admin.login-Form');
});



require __DIR__.'/admin.php';