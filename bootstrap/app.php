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
        'admin' => \App\Http\Middleware\AuthAdmin::class,
        'employee' => \App\Http\Middleware\Employee::class,
        'ceo'      => \App\Http\Middleware\Ceo::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,

    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
