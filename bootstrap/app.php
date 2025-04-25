<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AddCustomHeader;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsCustomer;
use App\Http\Middleware\EnsureUserIsDelivery;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         // Add your middleware here
         $middleware->alias([
            'custom_header' => AddCustomHeader::class,
            'isAdmin' => EnsureUserIsAdmin::class,
            'isCustomer' => EnsureUserIsCustomer::class,
            'isDelivery' => EnsureUserIsDelivery::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
