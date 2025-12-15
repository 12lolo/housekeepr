<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'owner.active' => \App\Http\Middleware\EnsureOwnerIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Make sure API routes return JSON for unauthenticated requests
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            return $request->is('api/*');
        });
    })
    ->create();

// Set the public path for Hostinger deployment
if (file_exists(dirname(__DIR__).'/public_html')) {
    $app->usePublicPath(dirname(__DIR__).'/public_html');
}

return $app;
