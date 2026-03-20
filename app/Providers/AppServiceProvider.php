<?php

namespace App\Providers;

use App\Services\GreetingService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

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
        Log::info('AppServiceProvider boot executed');
        Response::macro('success', function ($data) {
            return response()->json([
                'success'=>true,
                'data'=>$data
            ]);
        });

        View::composer('*', function($view){
            $view->with('current_user',auth()->user());
        });

        View::share('app_name','admin_panel');

        Blade::directive('currency',function($amount){
            return "<?php echo '₹' . number_formate($amount);?>";
        });

    }
}
