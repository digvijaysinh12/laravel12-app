<?php

namespace App\Services\Admin;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
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

        if (in_array($order->status, ['paid', 'shipped', 'delivered'], true)) {
            Log::info('Broadcasting order status update event', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
            ]);

            event(new OrderStatusUpdated($order));
        }

        $this->clearAdminAnalyticsCache();

        return $order;
    }

    public function promotePendingOrdersToProcessing(): int
    {
        return Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'processing']);
    }

    private function clearAdminAnalyticsCache(): void
    {
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['admin'])->flush();

            return;
        }

        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.recent.orders');
        Cache::forget('admin.sales.analytics');
    }
}
