<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request): View
    {
        $startedAt = microtime(true);
        $result = $this->productService->getAllProducts($request, 'admin');
        $categories = $this->productService->getAllCategories();
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Admin product index loaded', [
            'cache_key' => $result['cache_key'],
            'duration_ms' => $loadTimeMs,
        ]);

        return view('admin.products.index', [
            'products' => $result['products'],
            'total_products' => $result['total'],
            'categories' => $categories,
            'selected_category' => $request->query('category_id'),
            'page_title' => 'Manage Products',
            'listing_route' => route('admin.products.index'),
            'load_time_ms' => $loadTimeMs,
        ]);
    }

    public function create(): View
    {
        $categories = $this->productService->getAllCategories();

        Log::channel('products')->info('Create product page opened', [
            'user_id' => auth()->id(),
        ]);

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Log::channel('products')->info('Controller: Store product');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $this->productService->createProduct($data);
        $this->flushProductAndAdminCaches();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product): View
    {
        Log::info('Controller: Edit page');

        $categories = $this->productService->getAllCategories();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
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
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Update failed');
        }
    }

    public function destroy(Product $product): RedirectResponse
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
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Delete failed');
        }
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            $products = $this->productService->getProductsForExport();

            fputcsv($file, ['name', 'price', 'description']);

            foreach ($products as $product) {
                fputcsv($file, [$product->name, $product->price, $product->description]);
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
