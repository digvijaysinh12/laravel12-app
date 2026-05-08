<?php

namespace App\Services\Customer;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class CartService
{
    private const TAX_PERCENT = 5;

    /**
     * Get cart from session.
     */
    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Get formatted cart items.
     */
    public function getCartItems(): array
    {
        $cart = $this->getCart();

        $items = [];

        foreach ($cart as $productId => $item) {

            $product = $this->getProduct((int) $productId);

            // Remove invalid products from cart
            if (! $product) {

                unset($cart[$productId]);

                $this->saveCart($cart);

                continue;
            }

            $items[$productId] = $this->formatCartItem(
                $product,
                (int) ($item['quantity'] ?? 1)
            );
        }

        return $items;
    }

    /**
     * Add product to cart.
     */
    public function add(int $productId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {

            $cart[$productId]['quantity']++;

        } else {

            $product = $this->getProduct($productId);

            if (! $product) {
                throw new ModelNotFoundException(
                    "Product {$productId} not found."
                );
            }

            $cart[$productId] = [
                'quantity' => 1,
            ];
        }

        $this->saveCart($cart);
        // dd($cart);
    }

    /**
     * Increase quantity.
     */
    public function increment(int $productId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {

            $cart[$productId]['quantity']++;

            $this->saveCart($cart);
        }
    }

    /**
     * Decrease quantity.
     */
    public function decrement(int $productId): void
    {
        $cart = $this->getCart();

        if (! isset($cart[$productId])) {
            return;
        }

        if ($cart[$productId]['quantity'] > 1) {

            $cart[$productId]['quantity']--;

        } else {

            unset($cart[$productId]);
        }

        $this->saveCart($cart);
    }

    /**
     * Remove item from cart.
     */
    public function remove(int $productId): void
    {
        $cart = $this->getCart();

        unset($cart[$productId]);

        $this->saveCart($cart);
    }

    /**
     * Clear cart.
     */
    public function clear(): void
    {
        session()->forget('cart');

        $this->clearCache();
    }

    /**
     * Cart financial summary.
     */
    public function getSummary(): array
    {
        $items = $this->getCartItems();

        $subtotal = round(
            collect($items)->sum(
                fn ($item) => $item['price'] * $item['quantity']
            ),
            2
        );

        $tax = round(
            ($subtotal * self::TAX_PERCENT) / 100,
            2
        );

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_percent' => self::TAX_PERCENT,
            'total' => round($subtotal + $tax, 2),
            'item_count' => collect($items)->sum('quantity'),
        ];
    }

    /**
     * Shipping calculation.
     */
    public function getShipping(): array
    {
        $quantity = collect(
            $this->getCartItems()
        )->sum('quantity');

        $amount = $this->calculateShipping($quantity);

        return [
            'amount' => $amount,
            'method' => $quantity
                ? __('Standard shipping')
                : __('No shipping'),
        ];
    }

    /**
     * Totals for AJAX responses.
     */
    public function getCartTotalsForResponse(): array
    {
        $summary = $this->getSummary();

        $shipping = $this->getShipping();

        return [
            'summary' => $summary,
            'shipping' => $shipping,
            'grand_total' => round(
                $summary['total'] + $shipping['amount'],
                2
            ),
        ];
    }

    /**
     * Get cached product.
     */
    private function getProduct(int $productId): ?Product
    {
        return Cache::remember(
            "product_{$productId}",
            now()->addMinutes(30),

            fn () => Product::select([
                'id',
                'name',
                'price',
                'image',
                'stock',
            ])->find($productId)
        );
    }

    /**
     * Format cart item.
     */
    private function formatCartItem(
        Product $product,
        int $quantity
    ): array {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'quantity' => max(1, $quantity),
            'image' => $product->image,
            'stock' => (int) $product->stock,
        ];
    }

    /**
     * Save cart and clear cache.
     */
    private function saveCart(array $cart): void
    {
        session()->put('cart', $cart);

        $this->clearCache();
    }

    /**
     * Clear cart-related cache.
     */
    private function clearCache(): void
    {
        Cache::forget('cart_summary');
        Cache::forget('cart_shipping');
    }

    /**
     * Shipping pricing logic.
     */
    private function calculateShipping(int $quantity): float
    {
        return match (true) {
            $quantity <= 0 => 0,
            $quantity <= 2 => 40,
            $quantity <= 5 => 70,
            default => 100,
        };
    }
}
