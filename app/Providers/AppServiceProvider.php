<?php

namespace App\Providers;

use App\Services\GreetingService;
use Illuminate\Support\ServiceProvider;
use App\Services\PaymentService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentService::class, function($app){
            return new PaymentService();
        });

        $this->app->bind('greeting',function($app){
            return new GreetingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        error_log("AppServiceProvider boot executed");

    }
}
