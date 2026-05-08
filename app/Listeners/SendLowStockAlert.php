<?php

namespace App\Listeners;

use App\Events\ProductStockLow;
use App\Notifications\ProductLowStock;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendLowStockAlert
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function handle(ProductStockLow $event): void
    {
        $product = $event->product;
        $throttleKey = 'mail.low-stock-alert.'.$product->id;

        if (Cache::has($throttleKey)) {
            Log::channel('mail')->info('Low stock alert throttled.', [
                'product_id' => $product->id,
            ]);

            return;
        }

        Cache::remember($throttleKey, now()->addHour(), fn () => true);

        $this->notificationService->notifyAdmins(new ProductLowStock($product), [
            'event' => ProductStockLow::class,
            'product_id' => $product->id,
        ]);
    }
}
