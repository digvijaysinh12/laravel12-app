<?php

namespace App\Services\Admin;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function getAllOrders(): LengthAwarePaginator
    {
        return Order::with('user')
            ->latest()
            ->paginate(10);
    }

    public function getOrderDetails(int $id): Order
    {
        return Order::with('items.product', 'user')
            ->findOrFail($id);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update([
            'status' => $status,
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

    public function promotePendingOrdersToProcessing(): int
    {
        $orders = Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($orders as $order) {
            $order->update(['status' => 'processing']);
        }

        return $orders->count();
    }
}
