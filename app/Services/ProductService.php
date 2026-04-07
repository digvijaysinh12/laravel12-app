<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProductService
{
    private const CACHE_KEY_REGISTRY = 'products.cache.keys';

    public function getAllProducts(Request $request): array
    {
        $context = $request->routeIs('admin.*') ? 'admin' : 'user';
        $page = (int) $request->query('page', 1);
        $filters = $this->extractListingFilters($request);
        $filterHash = md5(json_encode($filters));
        $cacheKey = "products.list.{$context}.page.{$page}.filters.{$filterHash}";

        $products = $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($filters) {
            $query = Product::query()->with('category');
            $this->applyCommonFilters($query, $filters);

            return $query
                ->paginate(9)
                ->appends(array_filter($filters, fn ($value) => $value !== null && $value !== ''));
        });

        return [
            'products' => $products,
            'total' => $products->total(),
            'cache_key' => $cacheKey,
        ];
    }

    public function getProductById(int $productId): Product
    {
        $cacheKey = "product.{$productId}";

        return $this->rememberWithMetrics($cacheKey, now()->addMinutes(30), function () use ($productId) {
            return Product::query()
                ->with('category')
                ->findOrFail($productId);
        });
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        $cacheKey = 'featured.products';

        return $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($limit) {
            $query = Product::query()->with('category');

            if (Schema::hasColumn('products', 'is_featured')) {
                $query->where('is_featured', true);
            }

            return $query
                ->latest('id')
                ->take($limit)
                ->get();
        });
    }

    public function getProductsByCategory(Request $request, int $categoryId): array
    {
        $page = (int) $request->query('page', 1);
        $filters = $this->extractListingFilters($request);
        $filterHash = md5(json_encode($filters));
        $cacheKey = "category.{$categoryId}.products.page.{$page}.filters.{$filterHash}";

        $products = $this->rememberWithMetrics($cacheKey, now()->addHours(2), function () use ($filters, $categoryId) {
            $query = Product::query()
                ->with('category')
                ->where('category_id', $categoryId);

            $this->applyCommonFilters($query, $filters);

            return $query
                ->paginate(9)
                ->appends(array_filter($filters, fn ($value) => $value !== null && $value !== ''));
        });

        return [
            'products' => $products,
            'total' => $products->total(),
            'cache_key' => $cacheKey,
        ];
    }

    public function getAllCategories()
    {
        return $this->rememberWithMetrics('products.categories.list', now()->addHours(2), function () {
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
            'product_id' => $product->id
        ]);

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): bool
    {
        Log::channel('products')->warning('Deleting product', [
            'product_id' => $product->id
        ]);

        return (bool) $product->delete();
    }

    public function getProduct(Product $product): Product
    {
        // Backward-compatible wrapper.
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
        Log::channel('products')->info('Product cache flushed using tracked keys', ['keys' => count($keys)]);
    }

    private function extractListingFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'category_id' => $request->query('category_id'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'sort' => $request->query('sort', 'newest'),
        ];
    }

    private function applyCommonFilters(Builder $query, array $filters): void
    {
        $search = (string) ($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function (Builder $q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->latest('id'),
        };
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
