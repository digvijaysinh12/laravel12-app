<?php

namespace App\Listeners;

use App\Events\ProductReviewed;
use Illuminate\Support\Facades\Log;

class UpdateProductRating
{
    public function handle(ProductReviewed $event): void
    {
        $product = $event->product;

        $avgRating = (float) ($product->reviews()->approved()->avg('rating') ?? 0);

        $product->update([
            'rating' => $avgRating
        ]);

        Log::channel('products')->info('Rating updated', [
            'product_id' => $product->id,
            'rating' => $avgRating
        ]);
    }
}
