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

        return view('cart.index', compact('cart'));
    }

    public function add($id)
    {
        $this->cartService->add($id);

        return back()->with('success','Added to cart');
    }

    public function clear()
    {

        $this->cartService->clear();

        return redirect()->route('cart.index')->with('success', 'Cart cleared');
    }

    public function increment($id)
    {
        $this->cartService->increment($id);

        return response()->json(['success' => true]);
    }

    public function decrement($id)
    {
        $this->cartService->decrement($id);

        return response()->json(['success' => true]);
    }

    public function remove($id)
    {
        $this->cartService->remove($id);

        return response()->json(['success' => true]);
    }
}