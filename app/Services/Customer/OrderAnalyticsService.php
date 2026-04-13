<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OrderAnalyticsService
{
    public function getAnalyticsForUser(User $user): array
    {
        return Cache::remember("customer.order.analytics.{$user->id}", now()->addMinutes(5), function () use ($user) {
            $orderStats = Order::query()
                ->where('user_id', $user->id)
                ->selectRaw('COUNT(*) as total_orders')
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total_spent')
                ->selectRaw('COALESCE(AVG(total_amount), 0) as average_order')
                ->first();

            $topProducts = OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->where('orders.user_id', $user->id)
                ->select('order_items.product_id')
                ->selectRaw('products.name as product_name')
                ->selectRaw('SUM(quantity) as quantity')
                ->groupBy('order_items.product_id', 'products.name')
                ->orderByDesc('quantity')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->product_name ?? 'N/A',
                        'quantity' => (int) $item->quantity,
                    ];
                })
                ->values()
                ->all();

            $ordersByStatus = Order::query()
                ->where('user_id', $user->id)
                ->select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->orderByDesc('total')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [
                        ($row->status ?: 'N/A') => (int) $row->total,
                    ];
                })
                ->all();

            return [
                'totalOrders' => (int) ($orderStats->total_orders ?? 0),
                'totalSpent' => (float) ($orderStats->total_spent ?? 0),
                'averageOrder' => (float) ($orderStats->average_order ?? 0),
                'topProducts' => $topProducts,
                'ordersByStatus' => $ordersByStatus,
            ];
        });
    }
}
