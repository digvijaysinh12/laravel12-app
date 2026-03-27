<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Services\DiscountService;
use App\Services\GreetingService;
use App\Services\ProductService;
use Illuminate\Pagination\Paginator;
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
            Log::info('PaymentService bind called');
            return new PaymentService();
        });

        $this->app->bind('greeting', function ($app) {
            return new GreetingService();
        });

        // signleton -> same instance reused
        $this->app->singleton(DiscountService::class, function($app){
            Log::info('DiscountService singletone created');
            return new DiscountService();
        });

        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Log::info('AppServiceProvider boot executed');
        Response::macro('success', function ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        });

        View::composer('*', function ($view) {
            $cart = session()->get('cart', []);
            $view->with('current_user', auth()->user());

            $cartCount = 0;

            foreach($cart as $item){
                $cartCount+= $item['quantity'];
            }
                $view->with('cartCount',$cartCount);
            
        });

        View::share('app_name', 'admin_panel');

        Blade::directive('currency', function ($amount) {
            return "<?php echo '₹' . number_format($amount);?>";
        });

        Paginator::useBootstrapFive();

    }
}
