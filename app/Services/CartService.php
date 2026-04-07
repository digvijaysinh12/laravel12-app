<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class CartService
{
    private const PRODUCT_CACHE_TTL_MINUTES = 30;
    private const CART_CACHE_TTL_MINUTES = 10;
    private const TAX_PERCENT = 5;

    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    public function getCartItems(bool $forceFreshProducts = false): array
    {
        $cart = $this->getCart();
        $items = [];
        $cartChanged = false;

        foreach ($cart as $productId => $item) {
            $product = $this->getCachedProduct((int) $productId, $forceFreshProducts);

            if (! $product) {
                unset($cart[$productId]);
                $cartChanged = true;
                continue;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            $items[$productId] = [
                'id' => (int) $product->id,
                'name' => (string) $product->name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'stock' => (int) ($product->stock ?? 0),
            ];
        }

        if ($cartChanged) {
            session()->put('cart', $items);
            $this->clearCartCache();
        }

        return $items;
    }

    public function getSummary(): array
    {
        $key = 'cart_summary_'.$this->getCartOwnerKey();

        return $this->customerCache()->remember($key, now()->addMinutes(self::CART_CACHE_TTL_MINUTES), function () {
            $items = $this->getCartItems();
            $subtotal = round(
                collect($items)->sum(fn ($item) => $item['price'] * $item['quantity']),
                2
            );
            $tax = round(($subtotal * self::TAX_PERCENT) / 100, 2);

            return [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'tax_percent' => self::TAX_PERCENT,
                'total' => round($subtotal + $tax, 2),
                'item_count' => (int) collect($items)->sum('quantity'),
            ];
        });
    }

    public function getShipping(?array $items = null): array
    {
        $key = 'cart_shipping_'.$this->getCartOwnerKey();

        return $this->customerCache()->remember($key, now()->addMinutes(self::CART_CACHE_TTL_MINUTES), function () use ($items) {
            $cartItems = $items ?? $this->getCartItems();
            $quantity = (int) collect($cartItems)->sum('quantity');

            $amount = match (true) {
                $quantity <= 0 => 0.0,
                $quantity <= 2 => 40.0,
                $quantity <= 5 => 70.0,
                default => 100.0,
            };

            return [
                'amount' => $amount,
                'method' => $quantity <= 0 ? 'No shipping' : 'Standard shipping',
                'quantity' => $quantity,
            ];
        });
    }

    public function clearCartCache(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['customer'])->flush();
            return;
        }

        Cache::forget('cart_summary_'.$this->getCartOwnerKey());
        Cache::forget('cart_shipping_'.$this->getCartOwnerKey());
    }

    public function add($productId): void
    {
        $cart = $this->getCart();
        $id = (int) $productId;

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $product = $this->getCachedProduct($id);

            if (! $product) {
                throw new ModelNotFoundException("Product {$id} not found.");
            }

            $cart[$id] = [
                'name' => (string) $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        session()->put('cart', $cart);
        $this->clearCartCache();
    }

    public function increment($productId): void
    {
        $cart = $this->getCart();
        $id = (int) $productId;

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
            session()->put('cart', $cart);
            $this->clearCartCache();
        }
    }

    public function decrement($productId): void
    {
        $cart = $this->getCart();
        $id = (int) $productId;

        if (isset($cart[$id])) {
            if ((int) $cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
            $this->clearCartCache();
        }
    }

    public function remove($productId): void
    {
        $cart = $this->getCart();
        $id = (int) $productId;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            $this->clearCartCache();
        }
    }

    public function clear(): void
    {
        session()->forget('cart');
        $this->clearCartCache();
    }

    public function getCartTotalsForResponse(): array
    {
        $summary = $this->getSummary();
        $shipping = $this->getShipping();
        $grandTotal = round($summary['total'] + $shipping['amount'], 2);

        return [
            'summary' => $summary,
            'shipping' => $shipping,
            'grand_total' => $grandTotal,
        ];
    }

    private function getCachedProduct(int $productId, bool $forceFresh = false): ?Product
    {
        $key = "product_{$productId}";

        if ($forceFresh) {
            $this->productCache()->forget($key);
        }

        return $this->productCache()->remember($key, now()->addMinutes(self::PRODUCT_CACHE_TTL_MINUTES), function () use ($productId) {
            return Product::query()
                ->select(['id', 'name', 'price', 'image', 'stock'])
                ->find($productId);
        });
    }

    private function getCartOwnerKey(): string
    {
        if (auth()->check()) {
            return 'user_'.auth()->id();
        }

        return 'session_'.session()->getId();
    }

    private function productCache()
    {
        return $this->supportsTags()
            ? Cache::tags(['products'])
            : Cache::store();
    }

    private function customerCache()
    {
        return $this->supportsTags()
            ? Cache::tags(['customer'])
            : Cache::store();
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
