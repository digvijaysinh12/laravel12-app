<?php

namespace App\Services\Reports;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CustomerReportService
{
    public function getData(): array
    {
        return Cache::remember('reports.customers', now()->addMinutes(15), function () {
            return [
                'total_customers' => User::count(),
                'new_customers_today' => User::whereDate('created_at', today())->count(),
                'customers_with_orders' => User::whereHas('orders')->count(),
            ];
        });
    }
}
