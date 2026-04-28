<?php

namespace App\Providers;

use App\Events\CartAbandoned;
use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Events\OrderStatusUpdated;
use App\Events\ProductAddedToCart;
use App\Events\ProductReviewed;
use App\Events\ProductStockChanged;
use App\Events\ProductViewed;
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

        OrderPlaced::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            LogOrderEvent::class,
        ],

        OrderPaid::class => [
            SendOrderEmail::class,
            NotifyAdmin::class,
            LogOrderEvent::class,
        ],

        OrderShipped::class => [
            SendOrderEmail::class,
            NotifyUser::class,
            LogOrderEvent::class,
        ],

        OrderDelivered::class => [
            SendOrderEmail::class,
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
            TrackAddToCart::class,
        ],

        CartAbandoned::class => [
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
