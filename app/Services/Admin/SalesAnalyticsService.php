<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;

class SalesAnalyticsService
{
    public function getAnalytics(): array
    {
        return Cache::remember('admin.sales.analytics', now()->addMinutes(5), function () {
            [$monthlySales, $topCustomers, $topProducts, $categorySales, $ordersCount] = Concurrency::run([
                fn () => $this->buildMonthlySales(),
                fn () => $this->buildTopCustomers(),
                fn () => $this->buildTopProducts(),
                fn () => $this->buildCategorySales(),
                fn () => Order::count(),
            ]);

            return [
                'monthlySales' => $monthlySales,
                'topCustomers' => $topCustomers,
                'topProducts' => $topProducts,
                'categorySales' => $categorySales,
                'summary' => [
                    'total_revenue' => (float) collect($monthlySales)->sum('revenue'),
                    'orders_count' => (int) $ordersCount,
                    'top_customer_count' => count($topCustomers),
                    'top_product_count' => count($topProducts),
                    'category_sales_count' => count($categorySales),
                ],
            ];
        });
    }

    public function getOrders(): Collection
    {
        return Order::query()
            ->select([
                'id',
                'user_id',
                'order_number',
                'total_amount',
                'status',
                'created_at',
            ])
            ->with('user:id,name')
            ->latest('id')
            ->get();
    }

    private function buildMonthlySales(): array
    {
        return Order::query()
            ->select(['created_at', 'total_amount'])
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn ($order) => Carbon::parse($order->created_at)->format('Y-m'))
            ->map(function (Collection $monthOrders) {
                return [
                    'revenue' => (float) $monthOrders->sum('total_amount'),
                    'average' => (float) $monthOrders->avg('total_amount'),
                    'orders_count' => $monthOrders->count(),
                ];
            })
            ->sortKeysDesc()
            ->all();
    }

    private function buildTopCustomers(): array
    {
        return Order::query()
            ->select('user_id')
            ->selectRaw('SUM(total_amount) as total_spent')
            ->selectRaw('COUNT(*) as orders')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'user_id' => (int) $row->user_id,
                    'total_spent' => (float) $row->total_spent,
                    'orders' => (int) $row->orders,
                ];
            })
            ->values()
            ->all();
    }

    private function buildTopProducts(): array
    {
        return OrderItem::query()
            ->select(['product_id', 'quantity'])
            ->with('product:id,name')
            ->get()
            ->groupBy('product_id')
            ->map(function (Collection $items) {
                $firstItem = $items->first();

                return [
                    'product_id' => (int) $firstItem->product_id,
                    'product_name' => $firstItem->product?->name ?? 'N/A',
                    'quantity' => (int) $items->sum('quantity'),
                ];
            })
            ->sortByDesc('quantity')
            ->take(10)
            ->values()
            ->all();
    }

    private function buildCategorySales(): array
    {
        return OrderItem::query()
            ->select(['product_id', 'quantity', 'price'])
            ->with('product.category:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'category_id' => (int) ($item->product?->category_id ?? 0),
                    'category_name' => $item->product?->category?->name ?? 'Uncategorized',
                    'revenue' => (float) $item->price * (int) $item->quantity,
                ];
            })
            ->groupBy('category_id')
            ->map(function (Collection $items) {
                $firstItem = $items->first();

                return [
                    'category_name' => $firstItem['category_name'],
                    'total_revenue' => (float) $items->sum('revenue'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->all();
    }
}
