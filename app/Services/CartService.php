<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
    public function getCart()
    {
        return Cart::with('items.product')
                ->where('user_id',auth()->id())
                ->first();
    }

    public function add($productId)
    {
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }

    public function remove($productId)
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->where('product_id', $productId)->delete();
        }
    }

    public function increment($productId)
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $item = $cart->items()->where('product_id', $productId)->first();

            if ($item) {
                $item->increment('quantity');
            }
        }
    }

    public function decrement($productId)
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $item = $cart->items()->where('product_id', $productId)->first();

            if ($item) {
                if ($item->quantity > 1) {
                    $item->decrement('quantity');
                } else {
                    $item->delete();
                }
            }
        }
    }

    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->delete();
        }
    }
}