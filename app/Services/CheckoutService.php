<?php

namespace App\Services;

use Exception;

class CheckoutService
{
    public function process()
    {
        $cart = session()->get('cart', []);

        if (!$cart || count($cart) == 0) {
            throw new Exception('Cart is empty');
        }

        $grandTotal = 0;
        $items = [];

        foreach ($cart as $productId => $item) {

            $total = $item['price'] * $item['quantity'];
            $grandTotal += $total;

            $items[] = [
                'product_id' => $productId,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $total
            ];
        }

        // Optional: clear cart after checkout
        session()->forget('cart');

        return [
            'invoice_no' => 'INV-' . rand(1000,9999),
            'date' => now(),
            'items' => $items,
            'grand_total' => $grandTotal,
            'user' => auth()->user()
        ];
    }
}