<?php

namespace App\Observers;

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
    }

    public function updated(Product $product): void
    {
        $this->invalidateCaches('updated', $product);
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
}


