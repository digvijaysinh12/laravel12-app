<?php

namespace App\Services\Reports;

use App\Models\Product;

class InventoryReportService
{
    public function getData()
    {
        $products = Product::all();

        return $products->map(function($product){
            return[
                $product->name,
                $product->stock,
                $product->price
            ];
        })->toArray();
    }
}