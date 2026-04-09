<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Customer\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function featured(): View
    {
        return view('user.home.index', [
            'featuredProducts' => $this->productService->getFeaturedProducts(),
        ]);
    }

    public function index(Request $request): View
    {
        $data = $this->productService->getAllProducts($request);

        return view('user.products.index', [
            'page_title' => 'All Products',
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $request->category_id,
            'listing_route' => route('user.products.index'),
        ]);
    }

    public function categoryProducts(Request $request, Category $category): View
    {
        $data = $this->productService->getProductsByCategory($request, $category->id);

        return view('user.products.index', [
            'page_title' => $category->name . ' Products',
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $category->id,
            'listing_route' => route('user.products.category', $category),
        ]);
    }

    public function show(Product $product): View
    {
        return view('user.products.show', [
            'product' => $this->productService->getProductById($product->id),
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
