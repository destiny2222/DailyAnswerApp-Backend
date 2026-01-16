<?php

use App\Http\Middleware\adminLogged;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
       $middleware->alias([
           'admin.logged_in'=> adminLogged::class,
           'adminLogged' => AdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            // '/dashboard/payment/*',
            // 'api/webhook/payment'
			// 'http://example.com/foo/bar',
			// 'http://example.com/foo/*',
            'api/*',
		]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
