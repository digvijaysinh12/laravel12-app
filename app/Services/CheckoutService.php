<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\ProductStockChanged;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckoutService
{
    public function process()
    {
        try {

            $cart = session('cart', []);

            if (empty($cart)) {
                throw new Exception('Your cart is empty. Please add items before checkout.');
            }

            $total = 0;
            $items = [];

            foreach ($cart as $id => $item) {
                if (!isset($item['price'], $item['quantity'])) {
                    throw new Exception('Invalid cart data detected.');
                }

                $lineTotal = $item['price'] * $item['quantity'];
                $total += $lineTotal;

                $items[] = [
                    'id' => $id,
                    'name' => $item['name'] ?? 'Product',
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $lineTotal
                ];
            }

            DB::beginTransaction();

            $orderNumber = 'ORD-' . date('Y') . '-' . strtoupper(uniqid());

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => 'COD',
                'payment_status' => 'pending',
                'shipping_address' => 'Default Address', // replace later
                'phone' => '9999999999'
            ]);

            foreach ($items as $item) {

                $product = Product::find($item['id']);

                if(!$product){
                    throw new ProductNotFoundException();
                }

                    if ($product->stock < $item['quantity']) {
                        throw new ProductOutOfStockException(
                            $product->name . ' is out of stock'
                        );
                    }   
                    
                    // reduce product quantity
                    $product->stock -= $item['quantity'];
                    $product->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                broadcast(new ProductStockChanged($product->id,$product->stock))->toOthers();
            }

            session()->forget('cart');

            DB::commit();

            Log::info('Broadcasting new order placed event', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'order_number' => $order->order_number,
            ]);

            event(new OrderPlaced($order));

            return [
                'success' => true,
                'invoice_no' => $order->order_number,
                'date' => now(),
                'items' => $items,
                'grand_total' => $total,
                'user' => auth()->user()
            ];

        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Checkout Failed', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            throw new Exception(
                app()->environment('local')
                    ? $e->getMessage() 
                    : 'Something went wrong while placing your order. Please try again.'
            );
        }
    }
}
