<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ProductCollection extends Collection
{
    // Only products with stock > 0
    public function inStock(): static
    {
        return $this->where('stock', '>', 0);
    }

    // Filter by price range
    public function byPriceRange($min = null, $max = null): static
    {
        return $this->filter(function ($product) use ($min, $max) {
            if ($min !== null && (float) $product->price < (float) $min) {
                return false;
            }

            if ($max !== null && (float) $product->price > (float) $max) {
                return false;
            }

            return true;
        });
    }

    // On Sale products (has discount)
    public function onSale(): static
    {
        return $this->filter(function ($product) {
            $discountPrice = $product->getAttribute('discount_price');

            if (! is_numeric($discountPrice)) {
                $discountPrice = $product->getAttribute('sale_price');
            }

            if (! is_numeric($discountPrice)) {
                return false;
            }

            return (float) $discountPrice < (float) $product->price;
        });
    }

    // Filter by multiple categories
    public function byCategories(array $categoryIds = []): static
    {
        if (empty($categoryIds)) {
            return $this;
        }

        return $this->whereIn('category_id', $categoryIds);
    }

    // Sorting
    public function sortProducts($sort): static
    {
        return match (strtolower((string) $sort)) {
            'price_asc' => $this->sortBy('price'),
            'price_desc' => $this->sortByDesc('price'),
            'name_asc' => $this->sortBy('name'),
            'popularity' => $this->sortByDesc(fn ($product) => (int) ($product->getAttribute('sales_count') ?? 0)),
            default => $this->sortByDesc('created_at'),
        };
    }
}
