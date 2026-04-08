<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ProductCollection extends Collection
{
    public function inStock()
    {
        return $this->filter(function ($product){
            return $product->stock >0;
        });
    }

    public function byPriceRange($min, $max)
    {
        return $this->filter(fn ($product) =>
            $product->price >= $min && $product->price <= $max
        );
    }

    public function featured()
    {
        return $this->filter(fn ($product) =>
            $product->is_featured ?? false
        );
    }

    public function onSale()
    {
        return $this->filter(fn ($product) =>
            !is_null($product->discount_price) &&
            $product->discount_price < $product->price
        );
    }

    public function totalValue()
    {
        return $this->sum(fn ($product) =>
            $product->price * $product->stock
        );
    }
    
}