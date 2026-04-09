<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CartService;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCartItems();
        $summary = $this->cartService->getSummary();
        $shipping = $this->cartService->getShipping($cart);
        $grandTotal = round($summary['total'] + $shipping['amount'], 2);

        return view('user.cart.index', compact('cart', 'summary', 'shipping', 'grandTotal'));
    }

    public function add($id)
    {
        $this->cartService->add($id);

        return back()->with('success', 'Added to cart');
    }

    public function remove($id)
    {
        $this->cartService->remove($id);
        $totals = $this->cartService->getCartTotalsForResponse();

        return response()->json([
            'grandTotal' => $totals['grand_total'],
            'subtotal' => $totals['summary']['subtotal'],
            'tax' => $totals['summary']['tax'],
            'shipping' => $totals['shipping']['amount'],
            'total' => $totals['grand_total'],
        ]);
    }

    public function clear()
    {
        $this->cartService->clear();

        return response()->json([
            'grandTotal' => 0,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'total' => 0,
        ]);
    }

    public function increment($id)
    {
        $this->cartService->increment($id);
        $cart = $this->cartService->getCartItems();
        $item = $cart[$id] ?? null;
        $totals = $this->cartService->getCartTotalsForResponse();

        return response()->json([
            'quantity' => $item['quantity'] ?? 0,
            'itemTotal' => $item ? round($item['price'] * $item['quantity'], 2) : 0,
            'grandTotal' => $totals['grand_total'],
            'subtotal' => $totals['summary']['subtotal'],
            'tax' => $totals['summary']['tax'],
            'shipping' => $totals['shipping']['amount'],
            'total' => $totals['grand_total'],
        ]);
    }

    public function decrement($id)
    {
        $this->cartService->decrement($id);
        $cart = $this->cartService->getCartItems();
        $item = $cart[$id] ?? null;
        $totals = $this->cartService->getCartTotalsForResponse();

        return response()->json([
            'quantity' => $item['quantity'] ?? 0,
            'itemTotal' => $item ? round($item['price'] * $item['quantity'], 2) : 0,
            'grandTotal' => $totals['grand_total'],
            'subtotal' => $totals['summary']['subtotal'],
            'tax' => $totals['summary']['tax'],
            'shipping' => $totals['shipping']['amount'],
            'total' => $totals['grand_total'],
        ]);
    }
}
