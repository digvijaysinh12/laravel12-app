<?php

namespace App\Listeners;

use App\Events\ProductReviewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductRating
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductReviewed $event)
    {
        $product = $event->product;

        $avgRating = $product->reviews()->avg('rating');

        $product->update([
            'rating' => $avgRating
        ]);

        \Log::channel('products')->info('Rating updated', [
            'product_id' => $product->id,
            'rating' => $avgRating
        ]);
    }
}
