<?php

namespace App\Services\Reports;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CustomerReportService
{
    public function getData(): array
    {
        // Cache customer report data for 15 minutes to reduce database load
        return Cache::remember('reports.customers', now()->addMinutes(15), function () {

            return [
                // Total number of registered users
                'total_customers' => User::count(),

                // Users registered today
                'new_customers_today' => User::whereDate('created_at', today())->count(),

                // Users who have placed at least one order
                // Using relation 'orders' to filter only active customers
                'customers_with_orders' => User::has('orders')->count(),
            ];
        });
    }
}