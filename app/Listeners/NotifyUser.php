<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\ProductAddedToCart;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;

class NotifyUser
{
    public function handle(object $event): void
    {
        $user = $this->resolveUser($event);

        if (! $user) {
            return;
        }

        if (! $this->shouldNotify($event)) {
            return;
        }

        $notification = $this->makeNotification($event, $user);

        if ($notification) {
            $user->notify($notification);

            Log::info('Customer notification dispatched.', [
                'event' => $event::class,
                'user_id' => $user->id,
                'notification' => $notification::class,
            ]);
        }
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

    private function makeNotification(object $event, User $user): ?SystemNotification
    {
        return match (true) {
            $event instanceof OrderPlaced => new SystemNotification(
                type: 'order',
                title: 'Order Placed',
                message: 'Your order '.$event->order->order_number.' was placed successfully.',
                userId: $user->id,
                actionUrl: route('user.orders.show', $event->order),
                icon: 'order',
            ),
            $event instanceof OrderPaid => new SystemNotification(
                type: 'order',
                title: 'Payment Received',
                message: 'Payment received for order '.$event->order->order_number.'.',
                userId: $user->id,
                actionUrl: route('user.orders.show', $event->order),
                icon: 'order',
            ),
            $event instanceof OrderDelivered => new SystemNotification(
                type: 'order',
                title: 'Order Delivered',
                message: 'Your order '.$event->order->order_number.' has been delivered.',
                userId: $user->id,
                actionUrl: route('user.orders.show', $event->order),
                icon: 'order',
            ),
            $event instanceof OrderStatusUpdated => new SystemNotification(
                type: 'order',
                title: 'Order Updated',
                message: 'Your order '.$event->order->order_number.' is now '.strtolower($event->order->status).'.',
                userId: $user->id,
                actionUrl: route('user.orders.show', $event->order),
                icon: 'order',
            ),
            $event instanceof CartAbandoned => new SystemNotification(
                type: 'cart',
                title: 'Cart Reminder',
                message: 'You still have items in your cart.',
                userId: $user->id,
                actionUrl: route('user.cart.index'),
                icon: 'cart',
            ),
            $event instanceof ProductAddedToCart => new SystemNotification(
                type: 'cart',
                title: 'Added to Cart',
                message: $event->product->name.' was added to your cart.',
                userId: $user->id,
                actionUrl: route('user.cart.index'),
                icon: 'cart',
            ),
            default => null,
        };
    }
}
