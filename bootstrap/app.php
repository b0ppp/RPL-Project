<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        channels: __DIR__.'/../routes/channels.php', // <-- TAMBAHKAN BARIS INI
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'receptionist' => \App\Http\Middleware\EnsureUserIsReceptionist::class, // <-- TAMBAHKAN INI
            'kitchen' => \App\Http\Middleware\EnsureUserIsKitchenStaff::class, // <-- TAMBAHKAN INI
            'delivery' => \App\Http\Middleware\EnsureUserIsDeliveryStaff::class, // <-- TAMBAHKAN INI
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
