<?php

namespace App\Observers;

use App\Events\ProductStockLow;
use App\Models\Product;
use App\Services\Customer\ProductService;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function created(Product $product): void
    {
        $this->invalidateCaches('created', $product);
        $this->dispatchLowStockAlert($product);
    }

    public function updated(Product $product): void
    {
        $this->invalidateCaches('updated', $product);
        $this->dispatchLowStockAlert($product);
    }

    public function deleted(Product $product): void
    {
        $this->invalidateCaches('deleted', $product);
    }

    private function invalidateCaches(string $event, Product $product): void
    {
        $this->productService->flushProductCaches();

        Log::channel('products')->info('Product observer invalidated product caches', [
            'event' => $event,
            'product_id' => $product->id,
        ]);
    }

    private function dispatchLowStockAlert(Product $product): void
    {
        $threshold = (int) config('mail.low_stock_threshold', 10);

        if ($product->stock > $threshold) {
            return;
        }

        if ($product->wasRecentlyCreated || $product->wasChanged('stock')) {
            event(new ProductStockLow($product->fresh()));
        }
    }
}


