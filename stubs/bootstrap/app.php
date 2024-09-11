<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php', __DIR__.'/../routes/starter.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            {{ middleware-global }}
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\UserActivityLog::class,
            {{ middleware-web }}
        ]);

        $middleware->api(append: [
            {{ middleware-api }}
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
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->reportable(function (Throwable $e) {
            //
        })->stop();

        $exceptions->renderable(function (Throwable $e, Request $request) {
            $request->merge([
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'stack_trace' => json_decode(json_encode($e->getTrace())),
            ]);
        });
    })->create();
