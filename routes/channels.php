<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.orders', function ($user) {
    // FIXED: simple admin check for admin notifications.
    return $user && $user->is_admin;
});

Broadcast::channel('admin.notifications', function ($user) {
    // FIXED: shared admin notification channel.
    return $user && $user->is_admin;
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);

    return $order && (int) $user->id === (int) $order->user_id;
});

Broadcast::channel('user.{id}.notifications', function ($user, $id) {
    // FIXED: user notification channel.
    return (int) $user->id === (int) $id;
});

Broadcast::channel('store.browsing', function ($user) {
    if (! $user) {
        return false;
    }

    return [
        'id' => (int) $user->id,
        'name' => (string) $user->name,
    ];
});
