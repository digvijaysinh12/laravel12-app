<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TrackProductView
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
    public function handle(ProductViewed $event)
    {
        Log::channel('customer')->info('Product viewed', [
            'product_id' => $event->product->id,
            'user_id' => $event->user->id ?? null,
        ]);
    }
}
