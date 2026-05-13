<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemErrorAlert;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            RateLimiter::for('notifications', function (Request $request) {
                return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
            });
        },
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Aliases
        $middleware->alias([
            'checkrole' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })

->withExceptions(function (Exceptions $exceptions): void {

    /*
    |--------------------------------------------------------------------------
    | SLACK ERROR ALERTS
    |--------------------------------------------------------------------------
    */

    $exceptions->report(function (Throwable $e) {

        // Only production
        if (! app()->environment('production')) {
            return;
        }

        // Ignore console errors
        if (app()->runningInConsole()) {
            return;
        }

        // Ignore 404 errors
        if ($e instanceof ModelNotFoundException) {
            return;
        }

        // Create unique exception signature
        $signature = md5(
            get_class($e) .
            $e->getMessage() .
            $e->getFile() .
            $e->getLine()
        );

        $cacheKey = 'slack_error_' . $signature;

        // Prevent Slack flooding
        if (! Cache::has($cacheKey)) {

            // Store for 10 minutes
            Cache::put(
                $cacheKey,
                true,
                now()->addMinutes(10)
            );

            Notification::route(
                'slack',
                config('services.slack.channels.errors')
            )->notify(
                new SystemErrorAlert(
                    $e,
                    request()->fullUrl()
                )
            );
        }
    });

    /*
    |--------------------------------------------------------------------------
    | 404 - Model Not Found
    |--------------------------------------------------------------------------
    */

    $exceptions->render(function (ModelNotFoundException $e, $request) {
        return response()->view('errors.404', [], 404);
    });

    /*
    |--------------------------------------------------------------------------
    | 403 - Forbidden
    |--------------------------------------------------------------------------
    */

    $exceptions->render(function (HttpException $e, $request) {

        if ($e->getStatusCode() === 403) {

            return response()->view('errors.403', [], 403);
        }
    });

    /*
    |--------------------------------------------------------------------------
    | 500 - Server Error
    |--------------------------------------------------------------------------
    */

    $exceptions->render(function (Throwable $e, $request) {

        if (app()->environment('production')) {

            return response()->view('errors.500', [], 500);
        }
    });

})

    ->create();
