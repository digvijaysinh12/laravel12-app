<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\OrderShipped;
use App\Events\NotificationBroadcast;
use App\Events\ProductAddedToCart;
use App\Models\Notification;
use App\Models\User;

class NotifyUser
{
    public function handle($event): void
    {
        $user = $this->resolveUser($event);

        if (! $user) {
            return;
        }

        if (! $this->shouldNotify($event)) {
            return;
        }

        $data = $this->makeNotification($event, $user);

        if (! $data) {
            return;
        }

        $notification = Notification::create($data);

        // FIXED: broadcast the same custom row data to the user channel.
        broadcast(new NotificationBroadcast(
            $this->payload($notification),
            'user.'.$user->id.'.notifications'
        ));
    }

    private function resolveUser($event): ?User
    {
        return match (true) {
            isset($event->user) && $event->user instanceof User => $event->user,
            isset($event->order) && $event->order?->user instanceof User => $event->order->user,
            default => null,
        };
    }

    private function shouldNotify($event): bool
    {
        // FIXED: only send customer alerts for meaningful order updates.
        if ($event instanceof OrderPlaced) {
            return true;
        }

        if ($event instanceof OrderPaid) {
            return true;
        }

        if ($event instanceof OrderShipped) {
            return true;
        }

        if ($event instanceof OrderDelivered) {
            return true;
        }

        if ($event instanceof OrderStatusUpdated) {
            return $event->order->status === 'confirmed';
        }

        if ($event instanceof CartAbandoned) {
            return true;
        }

        if ($event instanceof ProductAddedToCart) {
            return true;
        }

        return false;
    }

    private function payload(Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'is_read' => $notification->is_read,
            'created_at' => $notification->created_at?->toDateTimeString(),
        ];
    }

    private function makeNotification($event, User $user): ?array
    {
        return match (true) {
            $event instanceof OrderPlaced => [
                'type' => 'order',
                'title' => 'Order Placed',
                'message' => 'Your order '.$event->order->order_number.' was placed successfully.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof OrderPaid => [
                'type' => 'order',
                'title' => 'Payment Received',
                'message' => 'Payment received for order '.$event->order->order_number.'.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof OrderShipped => [
                'type' => 'order',
                'title' => 'Order Shipped',
                'message' => 'Your order '.$event->order->order_number.' has been shipped.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof OrderDelivered => [
                'type' => 'order',
                'title' => 'Order Delivered',
                'message' => 'Your order '.$event->order->order_number.' has been delivered.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof OrderStatusUpdated => [
                'type' => 'order',
                'title' => 'Order Updated',
                'message' => 'Your order '.$event->order->order_number.' is now '.strtolower($event->order->status).'.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof CartAbandoned => [
                'type' => 'cart',
                'title' => 'Cart Reminder',
                'message' => 'You still have items in your cart.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            $event instanceof ProductAddedToCart => [
                'type' => 'cart',
                'title' => 'Added to Cart',
                'message' => $event->product->name.' was added to your cart.',
                'user_id' => $user->id,
                'is_read' => false,
            ],
            default => null,
        };
    }
}
