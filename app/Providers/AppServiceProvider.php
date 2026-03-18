<?php

namespace App\Providers;

use App\Services\GreetingService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Services\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->bind('greeting', function ($app) {
            return new GreetingService();
        });

        $this->app->bind('product',ProductService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        error_log("AppServiceProvider boot executed");

        Response::macro('success', function ($message) {
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        });

    }
}
