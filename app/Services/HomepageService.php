<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class HomepageService
{
    public function getHomePageData()
    {

        $featured = Product::where('is_featured', true)
            ->take(8)
            ->get();

        $newArrivals = Product::latest()
            ->take(8)
            ->get();

        $onSale = Product::inRandomOrder()
            ->take(8)
            ->get();

        $categories = Category::all();


        return [
            'featured' => $featured,
            'newArrivals' => $newArrivals,
            'onSale' => $onSale,
            'categories' => $categories,
        ];
    }
}