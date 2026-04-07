<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.orders', function ($user) {
    return $user && $user->role === 'admin';
});

Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('store.browsing', function ($user) {
    if (! $user) {
        return false;
    }

    return [
        'id' => (int) $user->id,
        'name' => (string) $user->name,
        'role' => (string) $user->role,
    ];
});
