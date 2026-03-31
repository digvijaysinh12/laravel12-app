<?php

namespace App\Services\Admin;

use App\Models\Order;


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

        return $order;
    }
}