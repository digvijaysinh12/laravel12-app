<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function featured()
    {
        $startedAt = microtime(true);
        $featuredProducts = $this->productService->getFeaturedProducts();
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Featured products loaded', [
            'count' => $featuredProducts->count(),
            'duration_ms' => $loadTimeMs,
        ]);

        return view('welcome', compact('featuredProducts', 'loadTimeMs'));
    }

    public function index(Request $request)
    {
        $startedAt = microtime(true);
        $result = $this->productService->getAllProducts($request);
        $categories = $this->productService->getAllCategories();
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Product index loaded', [
            'cache_key' => $result['cache_key'],
            'duration_ms' => $loadTimeMs,
        ]);

        $view = $request->routeIs('admin.*')
            ? 'admin.products.index'
            : 'user.products.index';

        return view($view, [
            'products' => $result['products'],
            'total_products' => $result['total'],
            'categories' => $categories,
            'selected_category' => $request->query('category_id'),
            'page_title' => $request->routeIs('admin.*') ? 'Manage Products' : 'Product Catalog',
            'listing_route' => route('user.products.index'),
            'load_time_ms' => $loadTimeMs,
        ]);
    }

    public function categoryProducts(Request $request, Category $category)
    {
        $startedAt = microtime(true);
        $result = $this->productService->getProductsByCategory($request, (int) $category->id);
        $categories = $this->productService->getAllCategories();
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Category products loaded', [
            'category_id' => $category->id,
            'cache_key' => $result['cache_key'],
            'duration_ms' => $loadTimeMs,
        ]);

        return view('user.products.index', [
            'products' => $result['products'],
            'total_products' => $result['total'],
            'categories' => $categories,
            'selected_category' => $category->id,
            'page_title' => "{$category->name} Products",
            'listing_route' => route('user.products.category', $category),
            'load_time_ms' => $loadTimeMs,
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        Log::channel('products')->info('Create product page opened', [
            'user_id' => auth()->id()
        ]);

        return view('admin.products.create', compact('categories'));
    }

    public function search(Request $request)
    {
        $category = $request->query('category');
        $price = $request->query('price');

        return [
            'category' => $category,
            'price' => $price
        ];
    }

    public function store(StoreProductRequest $request)
    {
        Log::channel('products')->info('Controller: Store product');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $this->productService->createProduct($data);
        $this->flushProductAndAdminCaches();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $startedAt = microtime(true);
        $product = $this->productService->getProductById((int) $product->id);
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Single product loaded', [
            'product_id' => $product->id,
            'duration_ms' => $loadTimeMs,
        ]);

        return view('user.products.show', compact('product', 'loadTimeMs'));
    }

    public function edit(Product $product)
    {
        Log::info('Controller: Edit page');

        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            Log::info('Controller: Update product');

            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $this->productService->updateProduct($data, $product);
            Cache::forget('product_'.$product->id);
            Cache::forget('product.'.$product->id);
            $this->flushProductAndAdminCaches();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (Exception $e) {
            Log::channel('products')->error('Controller: Update failed', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Update failed');
        }
    }

    public function destroy(Product $product)
    {
        try {
            Log::channel('products')->warning('Controller: Delete product');

            $this->productService->deleteProduct($product);
            Cache::forget('product_'.$product->id);
            Cache::forget('product.'.$product->id);
            $this->flushProductAndAdminCaches();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully');
        } catch (Exception $e) {
            Log::channel('products')->error('Controller: Delete failed', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Delete failed');
        }
    }

    public function apiProducts()
    {
        Log::channel('products')->info('API products fetched', [
            'user_id' => auth()->id()
        ]);

        $products = Product::all();

        return response()->success($products);
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $products = Product::all();
            $file = fopen('php://output', 'w');

            fputcsv($file, ['name', 'price', 'description']);

            foreach ($products as $p) {
                fputcsv($file, [$p->name, $p->price, $p->description]);
            }
        }, 'products.csv');
    }

    private function flushProductAndAdminCaches(): void
    {
        $this->productService->flushProductCaches();

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['admin'])->flush();
            return;
        }

        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.recent.orders');
    }
}
