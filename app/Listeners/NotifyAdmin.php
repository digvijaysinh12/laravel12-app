<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\ProductAddedToCart;
use App\Events\ProductReviewed;
use App\Events\ProductStockChanged;
use App\Events\ProductViewed;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\NewOrderReceived;
use App\Notifications\ProductLowStock;
use App\Notifications\SystemNotification;
use App\Services\NotificationService;

class NotifyAdmin
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function handle(object $event): void
    {
        match (true) {
            $event instanceof OrderPlaced => $this->sendNewOrderNotification($event->order),
            $event instanceof ProductStockChanged => $this->sendLowStockNotification($event->productId),
            $event instanceof OrderPaid => $this->sendSystemNotification(
                'order',
                'Payment Received',
                'Order '.$event->order->order_number.' has been marked as paid.',
                route('admin.orders.show', $event->order)
            ),
            default => $this->sendSystemNotificationFromEvent($event),
        };
    }

    private function sendNewOrderNotification(Order $order): void
    {
        $order->loadMissing('user');

        $this->notificationService->notifyAdmins(new NewOrderReceived($order), [
            'event' => OrderPlaced::class,
            'order_id' => $order->id,
        ]);
    }

    private function sendLowStockNotification(int $productId): void
    {
        $product = Product::query()->find($productId);

        if (! $product || (int) $product->stock > (int) config('mail.low_stock_threshold', 10)) {
            return;
        }

        $this->notificationService->notifyAdmins(new ProductLowStock($product), [
            'event' => ProductStockChanged::class,
            'product_id' => $productId,
        ]);
    }

    private function sendSystemNotificationFromEvent(object $event): void
    {
        $notification = match (true) {
            $event instanceof ProductAddedToCart => new SystemNotification(
                type: 'cart',
                title: 'Added to Cart',
                message: $event->user->name.' added '.$event->product->name.' to cart',
                audience: 'admin',
                actionUrl: route('admin.dashboard'),
                icon: 'cart',
            ),
            $event instanceof CartAbandoned => new SystemNotification(
                type: 'cart',
                title: 'Cart Abandoned',
                message: $event->user->name.' abandoned a cart',
                audience: 'admin',
                actionUrl: route('admin.dashboard'),
                icon: 'cart',
            ),
            $event instanceof ProductReviewed => new SystemNotification(
                type: 'product',
                title: 'Product Reviewed',
                message: $event->product->name.' received a new review',
                audience: 'admin',
                actionUrl: route('admin.reviews.index'),
                icon: 'product',
            ),
            $event instanceof ProductViewed => new SystemNotification(
                type: 'product',
                title: 'Product Viewed',
                message: $event->user->name.' viewed '.$event->product->name,
                audience: 'admin',
                actionUrl: route('admin.dashboard'),
                icon: 'product',
            ),
            default => null,
        };

        if (! $notification) {
            return;
        }

        $this->notificationService->notifyAdmins($notification, [
            'event' => $event::class,
        ]);
    }

    private function sendSystemNotification(string $type, string $title, string $message, string $actionUrl): void
    {
        $this->notificationService->notifyAdmins(new SystemNotification(
            type: $type,
            title: $title,
            message: $message,
            audience: 'admin',
            actionUrl: $actionUrl,
            icon: 'order',
        ), [
            'title' => $title,
        ]);
    }
}
