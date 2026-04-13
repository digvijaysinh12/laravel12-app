<?php

namespace App\Services\Reports;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportService
{
    public function getData($days): array
    {
        $days = max(1, (int) $days);

        return Cache::remember("reports.sales.{$days}", now()->addMinutes(15), function () use ($days) {
            $totals = Order::query()
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('COUNT(*) as total_orders')
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
                ->first();

            return [
                // Simple summary output for the report file.
                'total_orders' => (int) ($totals->total_orders ?? 0),
                'total_revenue' => (float) ($totals->total_revenue ?? 0),
            ];
        });
    }

    public function exportCsv(int $days = 30): StreamedResponse
    {
        $data = $this->getData($days);

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, array_keys($data));
            fputcsv($handle, array_values($data));

            fclose($handle);
        }, 'sales-report.csv');
    }
}
