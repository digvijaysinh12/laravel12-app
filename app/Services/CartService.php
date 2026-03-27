<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    public function getCart()
    {
        return session()->get('cart',[]);
    }

    public function add($productId)
    {   
        //get current cart from session
        $cart = session()->get('cart',[]);

        // check if already exist
        if(isset($cart[$productId])){
            $cart[$productId]['quantity']++;
        }else{
            $product = Product::findOrFail($productId);

            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        session()->put('cart',$cart);
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
        $cart = session()->get('cart',[]);

        if(isset($cart[$productId])){
            $cart[$productId]['quantity']++;
        }

        session()->put('cart',$cart);
    }

    public function decrement($productId)
    {
        $cart = session()->get('cart',[]);

        if(isset($cart[$productId])){
            if($cart[$productId]['quantity']>1){
                $cart[$productId]['quantity']--;
            }else{
                unset($cart[$productId]);
            }
        }

        session()->put('cart',$cart);
    }

    public function clear()
    {
        session()->forget('cart');
    }
}