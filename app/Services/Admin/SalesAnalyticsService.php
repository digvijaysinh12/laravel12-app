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
        $monthlySales = [];

        Order::query()
            ->select(['id', 'created_at', 'total_amount'])
            ->orderBy('id')
            ->chunkById(1000, function (Collection $orders) use (&$monthlySales) {
                foreach ($orders as $order) {
                    $month = Carbon::parse($order->created_at)->format('Y-m');

                    if (! isset($monthlySales[$month])) {
                        $monthlySales[$month] = [
                            'revenue' => 0.0,
                            'orders_count' => 0,
                        ];
                    }

                    $monthlySales[$month]['revenue'] += (float) $order->total_amount;
                    $monthlySales[$month]['orders_count']++;
                }
            }, 'id');

        return collect($monthlySales)
            ->map(function (array $monthData) {
                return [
                    'revenue' => (float) $monthData['revenue'],
                    'average' => $monthData['orders_count'] > 0
                        ? round($monthData['revenue'] / $monthData['orders_count'], 2)
                        : 0.0,
                    'orders_count' => (int) $monthData['orders_count'],
                ];
            })
            ->sortKeysDesc()
            ->all();
    }

    private function buildTopCustomers(): array
    {
        $topCustomers = [];

        Order::query()
            ->select(['id', 'user_id', 'total_amount'])
            ->orderBy('id')
            ->chunkById(1000, function (Collection $orders) use (&$topCustomers) {
                foreach ($orders as $order) {
                    $userId = (int) $order->user_id;

                    if (! isset($topCustomers[$userId])) {
                        $topCustomers[$userId] = [
                            'user_id' => $userId,
                            'total_spent' => 0.0,
                            'orders' => 0,
                        ];
                    }

                    $topCustomers[$userId]['total_spent'] += (float) $order->total_amount;
                    $topCustomers[$userId]['orders']++;
                }
            }, 'id');

        return collect($topCustomers)
            ->sortByDesc('total_spent')
            ->take(10)
            ->values()
            ->map(function (array $row) {
                return [
                    'user_id' => (int) $row['user_id'],
                    'total_spent' => (float) $row['total_spent'],
                    'orders' => (int) $row['orders'],
                ];
            })
            ->all();
    }

    private function buildTopProducts(): array
    {
        $topProducts = [];

        OrderItem::query()
            ->select(['id', 'product_id', 'quantity'])
            ->with('product:id,name')
            ->orderBy('id')
            ->chunkById(1000, function (Collection $items) use (&$topProducts) {
                foreach ($items as $item) {
                    $productId = (int) $item->product_id;

                    if (! isset($topProducts[$productId])) {
                        $topProducts[$productId] = [
                            'product_id' => $productId,
                            'product_name' => $item->product?->name ?? 'N/A',
                            'quantity' => 0,
                        ];
                    }

                    $topProducts[$productId]['quantity'] += (int) $item->quantity;
                }
            }, 'id');

        return collect($topProducts)
            ->sortByDesc('quantity')
            ->take(10)
            ->values()
            ->map(function (array $row) {
                return [
                    'product_id' => (int) $row['product_id'],
                    'product_name' => $row['product_name'],
                    'quantity' => (int) $row['quantity'],
                ];
            })
            ->all();
    }

    private function buildCategorySales(): array
    {
        $categorySales = [];

        OrderItem::query()
            ->select(['id', 'product_id', 'quantity', 'price'])
            ->with('product.category:id,name')
            ->orderBy('id')
            ->chunkById(1000, function (Collection $items) use (&$categorySales) {
                foreach ($items as $item) {
                    $categoryId = (int) ($item->product?->category_id ?? 0);
                    $categoryName = $item->product?->category?->name ?? 'Uncategorized';

                    if (! isset($categorySales[$categoryId])) {
                        $categorySales[$categoryId] = [
                            'category_id' => $categoryId,
                            'category_name' => $categoryName,
                            'total_revenue' => 0.0,
                        ];
                    }

                    $categorySales[$categoryId]['total_revenue'] += (float) $item->price * (int) $item->quantity;
                }
            }, 'id');

        return collect($categorySales)
            ->sortByDesc('total_revenue')
            ->map(function (array $row) {
                return [
                    'category_name' => $row['category_name'],
                    'total_revenue' => (float) $row['total_revenue'],
                ];
            })
            ->all();
    }
}
