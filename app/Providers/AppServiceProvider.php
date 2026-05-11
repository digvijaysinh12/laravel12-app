<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\DiscountService;
use App\Services\GreetingService;
use App\Services\Customer\PaymentService;
use App\Services\ExternalApiService;
use App\Support\Notifications\NotificationViewData;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
            return new PaymentService();
        });

        $this->app->bind('greeting', function () {
            return new GreetingService();
        });

        // Singleton: same instance reused.
        $this->app->singleton(DiscountService::class, function () {
            return new DiscountService();
        });

        $this->app->singleton(ExternalApiService::class,function(){
            return new ExternalApiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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

        View::composer('admin.layouts.app', function ($view) {
            if (! auth()->check()) {
                $view->with('adminNotificationData', [
                    'audience' => 'admin',
                    'unreadCount' => 0,
                    'latestNotifications' => [],
                ]);

                return;
            }

            $view->with(
                'adminNotificationData',
                app(NotificationViewData::class)->forUser(auth()->user())
            );
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
