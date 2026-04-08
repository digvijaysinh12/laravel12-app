<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\User;

class OrderAnalyticsService
{
    public function getAnalyticsForUser(User $user): array
    {
        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->get();

        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('total_amount');
        $averageOrder = $orders->avg('total_amount') ?? 0;

        $topProducts = $orders->flatMap->items
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product_name' => $items->first()->product->name ?? 'N/A',
                    'quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('quantity')
            ->take(3)
            ->values();

        $ordersByStatus = $orders->groupBy('status')
            ->map(fn ($group) => $group->count())
            ->sortByDesc(fn ($count) => $count);

        return compact(
            'totalOrders',
            'totalSpent',
            'averageOrder',
            'topProducts',
            'ordersByStatus'
        );
    }
}
