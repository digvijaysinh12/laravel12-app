<?php

namespace App\Services\Reports;

use App\Models\Order;

class SalesReportService
{
    public function getData($days){

        $orders = Order::where('created_at', '>=',now()->subDays($days))->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return [
            ['Total Orders', $totalOrders],
            ['Total Revenue',$totalRevenue]
        ];
    }
}