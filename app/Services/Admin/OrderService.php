<?php

namespace App\Services\Admin;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Support\Facades\Log;


class OrderService
{
    public function getAllOrders()
    {
        return Order::with('user')
            ->latest()
            ->paginate(10);
    }

    public function getOrderDetails($id)
    {
        return Order::with('items.product', 'user')
            ->findOrFail($id);
    }

    public function updateStatus($order, $status)
    {
        $order->update([
            'status' => $status
        ]);

        $order->loadMissing('user');

        Log::info('Broadcasting order status update event', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $order->status,
        ]);

        event(new OrderStatusUpdated($order));

        return $order;
    }
}
