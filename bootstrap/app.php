<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkrole' => \App\Http\Middleware\CheckRole::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // 🔹 404 - Model Not Found
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return response()->view('errors.404', [], 404);
        });

        // 🔹 403 - Forbidden
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }
        });

        // 🔹 500 - Server Error (ONLY in production)
        $exceptions->render(function (\Throwable $e, $request) {
            if (app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }
        });

    })

    ->create();