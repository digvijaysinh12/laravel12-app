<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use App\Services\Customer\RecentlyViewedService;
use Illuminate\Support\Facades\Log;

class TrackProductView
{
    public function __construct(private readonly RecentlyViewedService $recentlyViewedService)
    {
    }

    public function handle(ProductViewed $event): void
    {
        $this->recentlyViewedService->record($event->product, $event->user);

        Log::channel('customer')->info('Product viewed', [
            'product_id' => $event->product->id,
            'user_id' => $event->user?->id,
        ]);
    }
}
