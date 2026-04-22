<?php

namespace App\Services\Admin;

use App\Models\Product;

class StockService
{
    public function reduce(Product $product, int $qty)
    {
        if ($product->stock < $qty) {
            throw new \RuntimeException("Not enough stock for {$product->name}");
        }

        $product->decrement('stock', $qty);
    }

    public function restore(Product $product, int $qty)
    {
        $product->increment('stock', $qty);
    }
}