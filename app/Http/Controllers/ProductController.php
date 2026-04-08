<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function featured(): View
    {
        return view('user.home', [
            'featuredProducts' => $this->productService->getFeaturedProducts()
        ]);
    }

    public function index(Request $request): View
    {
        $data = $this->productService->getAllProducts($request);

        return view('user.products.index', [
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $request->category_id,
        ]);
    }

    public function categoryProducts(Request $request, Category $category): View
    {
        $data = $this->productService->getProductsByCategory($request, $category->id);

        return view('user.products.category', [
            'products' => $data['products'],
            'total_products' => $data['total'],
            'categories' => $this->productService->getAllCategories(),
            'selected_category' => $category->id,
        ]);
    }

    public function show(Product $product): View
    {
        return view('user.products.show', [
            'product' => $this->productService->getProductById($product->id)
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