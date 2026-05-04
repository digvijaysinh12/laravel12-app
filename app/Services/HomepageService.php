<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomepageService
{
    public function getHomePageData()
    {
        return Cache::remember('homepage.data', 3600, function () {

            $featured = Product::where('is_featured', true)->take(8)->get();
            $newArrivals = Product::latest()->take(8)->get();
            $onSale = Product::inRandomOrder()->take(8)->get();
            $categories = Category::all();

            return [
                'featured' => $featured,
                'newArrivals' => $newArrivals,
                'onSale' => $onSale,
                'categories' => $categories,
            ];

        });
    }
}
