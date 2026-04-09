<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ProductCollection extends Collection
{
    // Only products with stock > 0
    public function inStock()
    {
        return $this->filter(fn ($product) => $product->stock > 0);
    }

    // Filter by price range
    public function byPriceRange($min = null, $max = null)
    {
        return $this->filter(function ($product) use ($min, $max) {
            if ($min !== null && $product->price < $min) {
                return false;
            }

            if ($max !== null && $product->price > $max) {
                return false;
            }

            return true;
        });
    }

    // Featured products
    public function featured()
    {
        return $this->where('is_featured', true);
    }

    // On Sale products (has discount)
    public function onSale()
    {
        return $this->filter(fn ($product) =>
            !is_null($product->discount_price) &&
            $product->discount_price < $product->price
        );
    }

    // Filter by multiple categories
    public function byCategories(array $categoryIds = [])
    {
        if (empty($categoryIds)) {
            return $this;
        }

        return $this->filter(fn ($product) =>
            in_array($product->category_id, $categoryIds)
        );
    }

    // Sorting
    public function sortProducts($sort)
    {
        return match ($sort) {
            'price_asc' => $this->sortBy('price'),
            'price_desc' => $this->sortByDesc('price'),
            'name_asc' => $this->sortBy('name'),
            'popularity' => $this->sortByDesc('sales_count'), // assume column exists
            default => $this->sortByDesc('id'), // newest
        };
    }

    // Total inventory value
    public function totalValue()
    {
        return $this->sum(fn ($product) =>
            $product->price * $product->stock
        );
    }
}