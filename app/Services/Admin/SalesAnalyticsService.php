<?php

namespace App\Services\Admin;

use App\Models\Order;
use Carbon\Carbon;

class SalesAnalyticsService
{
    public function getAnalytics(): array
    {
        $orders = Order::with('user', 'items.product.category')->get();

        $monthlySales = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m');
        })
            ->map(function ($monthOrders) {
                return [
                    'total_revenue' => $monthOrders->sum('total_amount'),
                    'average_order' => $monthOrders->avg('total_amount'),
                    'orders_count' => $monthOrders->count(),
                ];
            })
            ->sortKeysDesc();

        $topCustomers = $orders
            ->groupBy('user_id')
            ->map(function ($userOrders) {
                return [
                    'user_id' => $userOrders->first()->user_id,
                    'total_spent' => $userOrders->sum('total_amount'),
                    'orders' => $userOrders->count(),
                ];
            })
            ->sortByDesc('total_spent')
            ->take(10);

        return compact('monthlySales', 'topCustomers');
    }

    public function getOrders()
    {
        return Order::with('user', 'items.product.category')->get();
    }
}
