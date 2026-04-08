<?php

namespace App\Services\Customer;

use App\Exceptions\InsufficientPermissionException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function paginateForUser(User $user): LengthAwarePaginator
    {
        return $user->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);
    }

    public function getOrderForUser(User $user, Order $order): Order
    {
        if ((int) $order->user_id !== (int) $user->id) {
            throw new InsufficientPermissionException('You do not have permission to view this order.');
        }

        return $order->load('items.product.category', 'user');
    }
}
