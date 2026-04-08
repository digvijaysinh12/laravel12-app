<?php
namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getAllProducts(Request $request): array
    {
        $cacheKey = $this->buildCacheKey('products', $request);

        return Cache::remember($cacheKey, 3600, function () use ($request) {

            $query = Product::query()->with('category');

            // Filters
            if ($request->search) {
                $query->where('name', 'like', "%{$request->search}%");
            }

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            // Sorting
            match ($request->sort) {
                'price_asc' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                default => $query->latest(),
            };

            $products = $query->paginate(9);

            return [
                'products' => $products,
                'total' => $products->total(),
            ];
        });
    }

    public function getProductsByCategory(Request $request, int $categoryId): array
    {
        $request->merge(['category_id' => $categoryId]);

        return $this->getAllProducts($request);
    }

    public function getProductById(int $id): Product
    {
        return Cache::remember("product.{$id}", 1800, function () use ($id) {
            return Product::with('category')->findOrFail($id);
        });
    }

    public function getFeaturedProducts()
    {
        return Cache::remember('products.featured', 3600, function () {
            return Product::where('is_featured', true)
                ->latest()
                ->take(8)
                ->get();
        });
    }

    public function getAllCategories()
    {
        return Cache::remember('products.categories', 7200, function () {
            return Category::orderBy('name')->get();
        });
    }

    public function getProductsForApi()
    {
        return Product::with('category')->latest()->get();
    }

    public function getProductsForExport()
    {
        return Product::orderBy('name')->get(['name', 'price', 'description']);
    }

    // 🔥 Helper: Clean cache key generator
    private function buildCacheKey(string $prefix, Request $request): string
    {
        return $prefix . '.' . md5(json_encode([
            'search' => $request->search,
            'category_id' => $request->category_id,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'sort' => $request->sort,
            'page' => $request->page,
        ]));
    }
}