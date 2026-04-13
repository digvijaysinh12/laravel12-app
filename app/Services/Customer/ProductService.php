<?php

namespace App\Services\Customer;

use App\Collections\ProductCollection;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductService
{
    private const CACHE_KEY_REGISTRY = 'products.cache.keys';

    public function getAllProducts(Request $request, array $overrides = []): array
    {
        $context = $request->routeIs('admin.*') ? 'admin' : 'user';
        $page = max(1, (int) $request->query('page', 1));
        $filters = $this->extractListingFilters($request, $overrides);

        $cacheKey = sprintf(
            'products.list.%s.page.%d.filters.%s',
            $context,
            $page,
            md5(json_encode($filters))
        );

        $products = $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($filters, $page, $request) {
            $allProducts = $this->buildProductQuery($filters)->get();

            $filteredProducts = $this->filterProducts($allProducts, $filters);
            $perPage = 9;

            $pageItems = $filteredProducts->forPage($page, $perPage)->values();

            return new LengthAwarePaginator(
                $pageItems,
                $filteredProducts->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->except('page'),
                ]
            );
        });

        return [
            'products' => $products,
            'total' => $products->total(),
            'cache_key' => $cacheKey,
        ];
    }

    public function getProductsByCategory(Request $request, int $categoryId): array
    {
        return $this->getAllProducts($request, [
            'category_ids' => [$categoryId],
        ]);
    }

    public function getProductById(int $productId): Product
    {
        $cacheKey = "product.{$productId}";

        return $this->rememberWithMetrics($cacheKey, now()->addMinutes(30), function () use ($productId) {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'price',
                    'description',
                    'category_id',
                    'stock',
                    'image',
                    'is_featured',
                    'created_at',
                ])
                ->with('category:id,name')
                ->findOrFail($productId);
        });
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        $cacheKey = 'products.featured';

        return $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($limit) {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'price',
                    'description',
                    'category_id',
                    'stock',
                    'image',
                    'is_featured',
                ])
                ->where('is_featured', true)
                ->with('category:id,name')
                ->latest('id')
                ->take($limit)
                ->get();
        });
    }

    public function getAllCategories()
    {
        return $this->rememberWithMetrics('products.categories', now()->addHours(2), function () {
            return Category::query()->orderBy('name')->get(['id', 'name']);
        });
    }

    public function createProduct(array $data): Product
    {
        Log::channel('products')->info('Creating product');

        return Product::create($data);
    }

    public function updateProduct(array $data, Product $product): Product
    {
        Log::channel('products')->info('Updating product', [
            'product_id' => $product->id,
        ]);

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): bool
    {
        Log::channel('products')->warning('Deleting product', [
            'product_id' => $product->id,
        ]);

        return (bool) $product->delete();
    }

    public function getProduct(Product $product): Product
    {
        return $this->getProductById((int) $product->id);
    }

    public function flushProductCaches(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['products'])->flush();
            Log::channel('products')->info('Product cache flushed using cache tags');

            return;
        }

        $keys = Cache::get(self::CACHE_KEY_REGISTRY, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget(self::CACHE_KEY_REGISTRY);

        Log::channel('products')->info('Product cache flushed using tracked keys', [
            'keys' => count($keys),
        ]);
    }

    public function getProductsForApi()
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'price',
                'description',
                'category_id',
                'stock',
                'image',
                'is_featured',
            ])
            ->with('category:id,name')
            ->latest('id')
            ->get();
    }

    public function getProductsForExport()
    {
        return Product::query()->orderBy('name')->get(['name', 'price', 'description']);
    }

    private function filterProducts(ProductCollection $products, array $filters): ProductCollection
    {
        if ($filters['search'] !== '') {
            $search = mb_strtolower($filters['search']);

            $products = $products->filter(function ($product) use ($search) {
                $haystack = mb_strtolower(implode(' ', array_filter([
                    (string) $product->name,
                    (string) $product->description,
                    (string) ($product->category?->name ?? ''),
                ])));

                return str_contains($haystack, $search);
            });
        }

        if ($filters['on_sale']) {
            $products = $products->onSale();
        }

        return $products->sortProducts($filters['sort'] ?? 'newest')->values();
    }

    private function extractListingFilters(Request $request, array $overrides = []): array
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category_ids' => collect($request->input('category_ids', $request->input('category_id', [])))
                ->filter(fn ($categoryId) => $categoryId !== null && $categoryId !== '')
                ->map(fn ($categoryId) => (int) $categoryId)
                ->values()
                ->all(),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'sort' => $request->query('sort', 'newest'),
            'in_stock' => $request->boolean('in_stock'),
            'on_sale' => $request->boolean('on_sale'),
        ];

        return array_replace($filters, $overrides);
    }

    private function buildProductQuery(array $filters)
    {
        $query = Product::query()
            ->select([
                'id',
                'name',
                'price',
                'description',
                'category_id',
                'stock',
                'image',
                'is_featured',
                'created_at',
            ])
            ->with('category:id,name')
            ->orderByDesc('created_at');

        if (! empty($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $query->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $query->where('price', '<=', $filters['max_price']);
        }

        if ($filters['in_stock']) {
            $query->where('stock', '>', 0);
        }

        if (($filters['sort'] ?? 'newest') === 'popularity') {
            $query->selectSub(function ($subQuery) {
                $subQuery->from('order_items')
                    ->selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('order_items.product_id', 'products.id');
            }, 'sales_count');
        }

        return $query;
    }

    private function rememberWithMetrics(string $cacheKey, $ttl, callable $callback)
    {
        $repository = $this->cacheRepository();
        $this->trackCacheKey($cacheKey);

        $cacheHit = $repository->has($cacheKey);
        $start = microtime(true);

        $value = $repository->remember($cacheKey, $ttl, $callback);

        $durationMs = round((microtime(true) - $start) * 1000, 2);

        Log::channel('products')->info('Product cache read', [
            'key' => $cacheKey,
            'hit' => $cacheHit,
            'duration_ms' => $durationMs,
        ]);

        return $value;
    }

    private function cacheRepository()
    {
        return $this->supportsTags()
            ? Cache::tags(['products'])
            : Cache::store();
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

    private function trackCacheKey(string $cacheKey): void
    {
        if ($this->supportsTags()) {
            return;
        }

        $keys = Cache::get(self::CACHE_KEY_REGISTRY, []);

        if (in_array($cacheKey, $keys, true)) {
            return;
        }

        $keys[] = $cacheKey;

        Cache::forever(self::CACHE_KEY_REGISTRY, $keys);
    }
}
