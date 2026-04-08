<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalesAnalyticsService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SalesAnalyticsController extends Controller
{
    public function __construct(protected SalesAnalyticsService $salesAnalyticsService)
    {
    }

    public function index(): View
    {
        return view('admin.sales.analytics', $this->salesAnalyticsService->getAnalytics());
    }

    public function export(): Response
    {
        $orders = $this->salesAnalyticsService->getOrders();

        return response()->streamDownload(function () use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['order_number', 'customer', 'total_amount', 'status', 'created_at']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user?->name ?? 'N/A',
                    $order->total_amount,
                    $order->status,
                    $order->created_at?->format('Y-m-d H:i:s'),
                ]);
            }
        }, 'sales-analytics.csv');
    }
}
