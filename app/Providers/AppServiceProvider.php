<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\DiscountService;
use App\Services\GreetingService;
use App\Services\PaymentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentService::class, function () {
            Log::info('PaymentService bind called');
            return new PaymentService();
        });

        $this->app->bind('greeting', function () {
            return new GreetingService();
        });

        // Singleton: same instance reused.
        $this->app->singleton(DiscountService::class, function () {
            Log::info('DiscountService singleton created');
            return new DiscountService();
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
            foreach ($cart as $item) {
                $cartCount += $item['quantity'];
            }

            $view->with('cartCount', $cartCount);
        });

        View::share('app_name', 'admin_panel');

        Blade::directive('currency', function ($amount) {
            return "<?php echo 'Rs. ' . number_format($amount, 2); ?>";
        });

        Paginator::useTailwind();

        // Product cache invalidation (created/updated/deleted).
        Product::observe(ProductObserver::class);
    }
}
