<?php

namespace App\Services\Reports;

use App\Models\Product;

class InventoryReportService
{
    public function count(): int
    {
        $productCount = Product::count();
        return max(1, min(10, $productCount) + 1); // summary + top 10 rows
    }

    public function generate(callable $progress): array
    {
        $lowThreshold = config('reports.low_stock_threshold', 10);

        $summary = [
            'Total Stock Quantity' => (int) Product::sum('stock'),
            'Total Stock Value' => 0.0, // recalculated below
            'Low Stock Products' => Product::where('stock', '<', $lowThreshold)->count(),
            'Out Of Stock' => Product::where('stock', 0)->count(),
        ];

        $summary['Total Stock Value'] = Product::select('stock', 'price')->get()
            ->reduce(fn($carry, $p) => $carry + ($p->stock * $p->price), 0.0);

        $progress(); // summary done

        $details = Product::select('id', 'name', 'stock', 'price')
            ->orderBy('stock')
            ->limit(10)
            ->get()
            ->map(function ($p) use ($progress) {
                $progress();
                return [
                    'Product' => $p->name,
                    'Stock' => $p->stock,
                    'Value' => $p->stock * $p->price,
                ];
            })
            ->toArray();

        return [
            'meta' => [
                'title' => 'Inventory Report',
                'generated_at' => now()->toDateTimeString(),
                'filters' => "Low threshold: {$lowThreshold}",
            ],
            'summary_headers' => array_keys($summary),
            'summary_rows' => [array_values($summary)],
            'details_headers' => ['Product', 'Stock', 'Value'],
            'details_rows' => $details,
        ];
    }
}
