<?php

namespace App\Services\Reports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    public function count(int $days): int
    {
        $from = now()->subDays($days);
        $groupCount = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', $from)
            ->distinct('order_items.product_id')
            ->count('order_items.product_id');

        return max(1, min(5, $groupCount) + 1); // summary + top products processed
    }

    public function generate(int $days, callable $progress): array
    {
        $from = now()->subDays($days);

        $summary = [
            'Total Revenue' => $this->totalRevenue($from),
            'Total Orders' => $this->orderCount($from),
            'Average Order Value' => $this->avgOrderValue($from),
        ];

        $details = $this->topProducts($from, $progress);

        $progress(); // ensure bar completes after summary

        return [
            'meta' => [
                'title' => 'Sales Report',
                'generated_at' => now()->toDateTimeString(),
                'filters' => "From {$from->toDateString()}",
            ],
            'summary_headers' => array_keys($summary),
            'summary_rows' => [array_values($summary)],
            'details_headers' => ['Product', 'Quantity Sold', 'Revenue'],
            'details_rows' => $details,
        ];
    }

    private function totalRevenue($from)
    {
        return Order::where('created_at', '>=', $from)->sum('total_amount');
    }

    private function orderCount($from)
    {
        return Order::where('created_at', '>=', $from)->count();
    }

    private function avgOrderValue($from)
    {
        $orders = Order::where('created_at', '>=', $from);
        $count = $orders->count();
        return $count ? round($orders->sum('total_amount') / $count, 2) : 0;
    }

    private function topProducts($from, callable $progress): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.created_at', '>=', $from)
            ->groupBy('order_items.product_id', 'products.name')
            ->select([
                'products.name as product',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue'),
            ])
            ->orderByDesc('qty')
            ->limit(5);

        $rows = [];

        $query->chunk(50, function ($chunk) use (&$rows, $progress) {
            foreach ($chunk as $row) {
                $rows[] = [
                    'Product' => $row->product,
                    'Quantity Sold' => (int) $row->qty,
                    'Revenue' => (float) $row->revenue,
                ];
                $progress();
            }
        });

        return $rows;
    }
}
