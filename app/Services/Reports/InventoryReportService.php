<?php

namespace App\Services\Reports;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class InventoryReportService
{
    public function getData(): array
    {
        // Cache inventory report for 15 minutes to avoid repeated heavy queries
        return Cache::remember('reports.inventory', now()->addMinutes(15), function () {

            return [
                // Total number of products in the system
                'total_products' => Product::count(),

                // Products with stock less than 10 (low stock threshold)
                'low_stock_products' => Product::where('stock', '<', 10)->count(),

                // Products completely out of stock
                'out_of_stock_products' => Product::where('stock', 0)->count(),

                // Total inventory value (sum of all product prices)
                // Note: this assumes price only; for accurate value use stock * price
                'total_value' => (float) Product::sum('price'),
            ];
        });
    }
}