<?php

namespace App\Services;
use Illuminate\Support\Facades\Concurrency;
use App\Models\Product;
use App\Models\Category;

class HomepageService
{
    public function getHomePageData()
    {

        [$featured, $newArrivals, $onSale, $categories] = Concurrency::run([
            
            fn () => Product::where('is_featured', true)
                ->take(8)
                ->toBase()
                ->get(),

            fn () => Product::latest()
                ->take(8)
                ->toBase()
                ->get(),

            fn () => Product::inRandomOrder()
                ->take(8)
                ->toBase()
                ->get(),

            fn () => Category::all()
                    ->toBase()
                    ,
        ]);

        return [
            'featured' => $featured,
            'newArrivals' => $newArrivals,
            'onSale' => $onSale,
            'categories' => $categories,
        ];
    }
}