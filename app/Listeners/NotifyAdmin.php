<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use App\Events\NotificationBroadcast;
use App\Events\OrderPlaced;
use App\Events\ProductAddedToCart;
use App\Events\ProductReviewed;
use App\Events\ProductStockChanged;
use App\Events\ProductViewed;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;

class NotifyAdmin
{
    public function handle($event): void
    {

        $data = $this->makeNotification($event);

        if (! $data) {
            return;
        }

        // $notification = Notification::create($data);

        // FIXED: broadcast the same custom row data to the admin channel.
        // broadcast(new NotificationBroadcast(
        //     $this->payload($notification),
        //     'admin.notifications'
        // ));
        // dd($data);
        $notification = AdminNotification::create($data);
        // Log::channel('admin_notification')->info('Admin notification created', [
        //     'id' => $notification->id,
        //     'title' => $notification->title,
        // ]);

        broadcast(new NotificationBroadcast(
            $this->payload($notification),
            'admin.notifications'
        ));

    }

    private function payload(AdminNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'is_read' => $notification->is_read,
            'created_at' => $notification->created_at?->toDateTimeString(),
        ];
    }

    private function makeNotification($event): ?array
    {
        return match (true) {
            $event instanceof OrderPlaced => [
                'type' => 'order',
                'title' => 'New Order',
                'message' => $event->order->user->name.' placed '.$event->order->order_number,
                'user_id' => null,
                'is_read' => false,
            ],
            $event instanceof ProductAddedToCart => [
                'type' => 'cart',
                'title' => 'Added to Cart',
                'message' => $event->user->name.' added '.$event->product->name.' to cart',
                'user_id' => null,
                'is_read' => false,
            ],
            $event instanceof CartAbandoned => [
                'type' => 'cart',
                'title' => 'Cart Abandoned',
                'message' => $event->user->name.' abandoned a cart',
                'user_id' => null,
                'is_read' => false,
            ],
            $event instanceof ProductReviewed => [
                'type' => 'product',
                'title' => 'Product Reviewed',
                'message' => $event->product->name.' received a new review',
                'user_id' => null,
                'is_read' => false,
            ],
            $event instanceof ProductViewed => [
                'type' => 'product',
                'title' => 'Product Viewed',
                'message' => $event->user->name.' viewed '.$event->product->name,
                'user_id' => null,
                'is_read' => false,
            ],
            $event instanceof ProductStockChanged => [
                'type' => 'product',
                'title' => 'Stock Updated',
                'message' => 'Product #'.$event->productId.' stock is now '.$event->stock,
                'user_id' => null,
                'is_read' => false,
            ],
            default => null,
        };
    }
}
