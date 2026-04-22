<?php

namespace App\Http\Controllers\Customer;

use App\Events\ProductViewed;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\HomepageService;
use App\Services\Customer\ProductService;
use App\Services\Customer\RecentlyViewedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function featured(HomepageService $service, RecentlyViewedService $recentlyViewedService): View
    {
        return view('home.index', array_merge(
            $service->getHomePageData(),
            [
                'recentlyViewedProducts' => $recentlyViewedService->getProducts(auth()->user(), 6),
            ]
        ));
    }

    public function index(Request $request, RecentlyViewedService $recentlyViewedService): View
    {
        $data = $this->productService->getAllProducts($request);

        return view('user.products.index', [
            'page_title' => 'All Products',
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $request->category_id,
            'listing_route' => route('user.products.index'),
            'recentlyViewedProducts' => $recentlyViewedService->getProducts($request->user(), 6),
        ]);
    }

    public function categoryProducts(Request $request, Category $category, RecentlyViewedService $recentlyViewedService): View
    {
        $data = $this->productService->getProductsByCategory($request, $category->id);

        return view('user.products.index', [
            'page_title' => $category->name . ' Products',
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $category->id,
            'listing_route' => route('user.products.category', $category),
            'recentlyViewedProducts' => $recentlyViewedService->getProducts($request->user(), 6),
        ]);
    }

    public function show(Product $product, RecentlyViewedService $recentlyViewedService): View
    {
        ProductViewed::dispatch($product, auth()->user());

        $product = $this->productService->getProductById($product->id);

        return view('user.products.show', [
            'product' => $product->load([
                'reviews' => fn ($query) => $query->approved()->with('user:id,name')->latest(),
            ]),
            'userReview' => auth()->check()
                ? $product->reviews()->where('user_id', auth()->id())->latest()->first()
                : null,
            'recentlyViewedProducts' => $recentlyViewedService->getProducts(auth()->user(), 6, $product->id),
        ]);
    }

    public function apiProducts(): JsonResponse
    {
        return response()->success(
            $this->productService->getProductsForApi()
        );
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['name', 'price', 'description']);

            foreach ($this->productService->getProductsForExport() as $p) {
                fputcsv($file, [$p->name, $p->price, $p->description]);
            }
        }, 'products.csv');
    }
}
