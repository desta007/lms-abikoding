<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'enrolled' => \App\Http\Middleware\EnsureEnrolled::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'midtrans.csp' => \App\Http\Middleware\DisableCSPForMidtrans::class,
        ]);
        
        $middleware->append(\App\Http\Middleware\SetLocale::class);
        
        // Exclude payment webhook from CSRF protection (Midtrans sends POST without CSRF token)
        $middleware->validateCsrfTokens(except: [
            'payments/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
