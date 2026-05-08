<?php

namespace App\Http\Controllers\Customer;

use App\Events\ProductAddedToCart;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Customer\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Show cart page.
     */
    public function index(): View
    {
        $cart = $this->cartService->getCartItems();

        $summary = $this->cartService->getSummary();

        $shipping = $this->cartService->getShipping($cart);

        $grandTotal = round(
            $summary['total'] + $shipping['amount'],
            2
        );

        // Track user's latest cart activity
        auth()->user()?->update([
            'last_cart_activity' => now(),
        ]);

        return view('user.cart.index', compact(
            'cart',
            'summary',
            'shipping',
            'grandTotal'
        ));
    }

    /**
     * Add product to cart.
     */
    public function add(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $this->cartService->add($id);

        // Notify admin analytics/event system
        ProductAddedToCart::dispatch(
            $product,
            auth()->user()
        );

        return back()->with(
            'success',
            __('Added to cart')
        );
    }

    /**
     * Remove product from cart.
     */
    public function remove(int $id): JsonResponse
    {
        $this->cartService->remove($id);

        return response()->json(
            $this->cartResponse()
        );
    }

    /**
     * Clear full cart.
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clear();

        return response()->json([
            'quantity' => 0,
            'itemTotal' => 0,
            'grandTotal' => 0,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'total' => 0,
        ]);
    }

    /**
     * Increase quantity.
     */
    public function increment(int $id): JsonResponse
    {
        $this->cartService->increment($id);

        return response()->json(
            $this->cartResponse($id)
        );
    }

    /**
     * Decrease quantity.
     */
    public function decrement(int $id): JsonResponse
    {
        $this->cartService->decrement($id);

        return response()->json(
            $this->cartResponse($id)
        );
    }

    /**
     * Build reusable cart JSON response.
     */
    private function cartResponse(?int $id = null): array
    {
        $cart = $this->cartService->getCartItems();

        $item = $id
            ? ($cart[$id] ?? null)
            : null;

        $totals = $this->cartService
            ->getCartTotalsForResponse();

        return [
            'quantity' => $item['quantity'] ?? 0,

            'itemTotal' => $item
                ? round($item['price'] * $item['quantity'], 2)
                : 0,

            'grandTotal' => $totals['grand_total'],

            'subtotal' => $totals['summary']['subtotal'],

            'tax' => $totals['summary']['tax'],

            'shipping' => $totals['shipping']['amount'],

            'total' => $totals['grand_total'],
        ];
    }
}
