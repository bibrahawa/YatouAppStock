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
    ->withMiddleware(function (Middleware $middleware) {

        // $middleware->add('guest', \App\Http\Middleware\RedirectIfAuthenticated::class);
        // $middleware->add('role', \App\Http\Middleware\RoleAccessGuard::class);
        // $middleware->add('permission', \App\Http\Middleware\PermissionAccessGuard::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
