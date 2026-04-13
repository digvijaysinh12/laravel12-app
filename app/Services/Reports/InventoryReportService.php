<?php

namespace App\Services\Reports;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class InventoryReportService
{
    public function getData(): array
    {
        return Cache::remember('reports.inventory', now()->addMinutes(15), function () {
            return [
                'total_products' => Product::count(),
                'low_stock_products' => Product::where('stock', '<', 10)->count(),
                'out_of_stock_products' => Product::where('stock', 0)->count(),
                'total_value' => (float) Product::sum('price'),
            ];
        });
    }
}
