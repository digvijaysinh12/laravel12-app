<?php

namespace App\Services\Reports;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class SalesReportService
{
    public function getData(int $days = 30): array
    {
        // Ensure minimum 1 day to avoid invalid queries
        $days = max(1, $days);

        // Cache sales report data for 15 minutes to reduce database load
        return Cache::remember("reports.sales.{$days}", now()->addMinutes(15), function () use ($days) {

            $totals = Order::query()
                // Filter orders within given time range
                ->where('created_at', '>=', now()->subDays($days))

                // Count total orders
                ->selectRaw('COUNT(*) as total_orders')

                // Sum total revenue (fallback to 0 if null)
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')

                ->first();

            return [
                // Total number of orders in given period
                'total_orders' => (int) ($totals->total_orders ?? 0),

                // Total revenue generated
                'total_revenue' => (float) ($totals->total_revenue ?? 0),
            ];
        });
    }
}