<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php', __DIR__.'/../routes/starter.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\UserActivityLog::class,
            {{ middleware-web }}
        ]);

        $middleware->alias([
            'SsoPortal' => \App\Http\Middleware\SsoPortal::class,
            {{ middleware-alias }}
        ]);

        // ...
    })
    ->withSchedule(function (Schedule $schedule) {
        {{ schedule }}
    })
    ->withExceptions(function (Exceptions $exceptions) {
        {{ exceptions }}
    })->create();
