<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService){
        $this->cartService = $cartService;
    }
    public function index()
    {
        $cart = $this->cartService->getCart();

        return view('user.cart.index', compact('cart'));
    }

    public function add($id)
    {
        $this->cartService->add($id);

        return back()->with('success','Added to cart');
    }

    public function remove($id)
    {
        $this->cartService->remove($id);

        $cart = $this->cartService->getCart();

        return response()->json([
            'grandTotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity'])
        ]);
    }

    public function clear()
    {
        $this->cartService->clear();

        return response()->json([
            'grandTotal' => 0
        ]);
    }

    public function increment($id)
    {
        $this->cartService->increment($id);

        $cart = $this->cartService->getCart();
        $item = $cart[$id];

        return response()->json([
            'quantity' => $item['quantity'],
            'itemTotal' => $item['price'] * $item['quantity'],
            'grandTotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity'])
        ]);
    }

    public function decrement($id)
    {
        $this->cartService->decrement($id);

        $cart = $this->cartService->getCart();

        if (!isset($cart[$id])) {
            return response()->json([
                'quantity' => 0,
                'itemTotal' => 0,
                'grandTotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity'])
            ]);
        }

        $item = $cart[$id];

        return response()->json([
            'quantity' => $item['quantity'],
            'itemTotal' => $item['price'] * $item['quantity'],
            'grandTotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity'])
        ]);
    }
}