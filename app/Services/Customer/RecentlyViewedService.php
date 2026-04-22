<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Models\User;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecentlyViewedService
{
    private const CACHE_TTL_MINUTES = 60 * 24 * 7;
    private const MAX_TRACKED_PRODUCTS = 12;

    public function record(Product $product, ?User $user): void
    {
        if (! $user || $user->role !== 'user') {
            return;
        }

        $key = $this->cacheKey($user);
        $productIds = collect($this->cacheRepository()->get($key, []))
            ->prepend((int) $product->id)
            ->unique()
            ->take(self::MAX_TRACKED_PRODUCTS)
            ->values()
            ->all();

        $this->cacheRepository()->put($key, $productIds, now()->addMinutes(self::CACHE_TTL_MINUTES));
    }

    public function getProducts(?User $user, int $limit = 6, ?int $excludeProductId = null): Collection
    {
        if (! $user || $user->role !== 'user') {
            return collect();
        }

        $productIds = collect($this->cacheRepository()->get($this->cacheKey($user), []))
            ->when($excludeProductId !== null, fn (Collection $ids) => $ids->reject(
                static fn (int $productId) => $productId === $excludeProductId
            ))
            ->take($limit)
            ->values();

        if ($productIds->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with('category:id,name')
            ->whereIn('id', $productIds->all())
            ->get()
            ->keyBy('id');

        return $productIds
            ->map(fn (int $productId) => $products->get($productId))
            ->filter()
            ->values();
    }

    private function cacheKey(User $user): string
    {
        return 'recently_viewed.user.'.$user->id;
    }

    private function cacheRepository()
    {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags(['customer']);
        }

        return Cache::store();
    }
}
