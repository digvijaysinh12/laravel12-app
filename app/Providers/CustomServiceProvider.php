<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class CustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('discount', function(){
            return 10;
        });

        View::composer('*',function($view){
            $view->with('companyName','My Company');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        config(['company.name' => "My Company"]);
    }
}
