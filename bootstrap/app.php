<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /**
         * Register the CORS middleware for the application.
         */
        $middleware->append(Illuminate\Http\Middleware\HandleCors::class);

        /**
         * Register the CSRF token verification middleware for the application.
         */
        $middleware->alias([
            'abilities' => \App\Http\Middleware\CustomCheckAbilities::class,
            'ability' => \App\Http\Middleware\CustomCheckAbility::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
