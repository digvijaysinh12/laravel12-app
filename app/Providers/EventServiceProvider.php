<?php

namespace App\Providers;

use App\Events\CartAbandoned;
use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\OrderShipped;
use App\Events\ProductAddedToCart;
use App\Events\ProductReviewed;
use App\Events\ProductViewed;
use App\Events\ProductStockChanged;
use App\Listeners\LogOrderEvent;
use App\Listeners\NotifyAdmin;
use App\Listeners\NotifyUser;
use App\Listeners\SendCartReminder;
use App\Listeners\SendOrderEmail;
use App\Listeners\TrackAddToCart;
use App\Listeners\TrackProductView;
use App\Listeners\UpdateProductRating;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // FIXED: keep the existing event/listener map simple.
        OrderPlaced::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        OrderPaid::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        OrderShipped::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        OrderDelivered::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        OrderStatusUpdated::class => [
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        ProductViewed::class => [
            NotifyAdmin::class,
            TrackProductView::class,
        ],

        ProductAddedToCart::class => [
            NotifyAdmin::class,
            NotifyUser::class,
            TrackAddToCart::class,
        ],

        CartAbandoned::class => [
            NotifyAdmin::class,
            NotifyUser::class,
            SendCartReminder::class,
        ],

        ProductReviewed::class => [
            NotifyAdmin::class,
            UpdateProductRating::class,
        ],

        ProductStockChanged::class => [
            NotifyAdmin::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
