<?php

namespace App\Services\Customer;

use App\Events\OrderPlaced;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CheckoutService
{
    public function process(array $data)
    {
        try {

            Log::info('Checkout START', ['user_id' => auth()->id()]);

            $cartService = app(CartService::class);

            $cart = $cartService->getCartItems(true);

            Log::info('Cart fetched', ['cart' => $cart]);

            if (empty($cart)) {
                Log::warning('Cart is empty', $cart);
                throw new Exception('Your cart is empty.');
            }

            $total = 0;
            $items = [];

            foreach ($cart as $id => $item) {
                Log::info('Processing cart item', ['product_id' => $id, 'item' => $item]);

                if (! isset($item['price'], $item['quantity'])) {
                    throw new Exception('Invalid cart data');
                }

                $lineTotal = $item['price'] * $item['quantity'];
                $total += $lineTotal;

                $items[] = [
                    'id' => $id,
                    'name' => $item['name'] ?? 'Product',
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $lineTotal,
                ];
            }

            Log::info('Cart processed', ['total' => $total]);

            DB::beginTransaction();
            Log::info('Transaction START');

            $orderNumber = 'ORD-'.date('Y').'-'.strtoupper(uniqid());

            $order = Order::create([
                'user_id' => auth()->id(),

                'full_name' => $data['name'],

                'order_number' => $orderNumber,

                'total_amount' => $total,

                'status' => 'pending',

                'payment_method' => 'COD',

                'payment_status' => 'pending',

                'shipping_address' => $data['address'],

                'phone' => $data['phone'],

                'city' => $data['city'],

                'pincode' => $data['pincode'],

                'notes' => $data['notes'] ?? null,
            ]);

            Log::info('Order created', ['order_id' => $order->id]);

            foreach ($items as $item) {
                $product = Product::find($item['id']);

                if (! $product) {
                    Log::error('Product not found', ['product_id' => $item['id']]);
                    throw new ProductNotFoundException;
                }

                if ($product->stock < $item['quantity']) {
                    Log::error('Out of stock', [
                        'product' => $product->name,
                        'stock' => $product->stock,
                    ]);
                    throw new ProductOutOfStockException($product->name.' out of stock');
                }

                $product->stock -= $item['quantity'];
                $product->save();

                Log::info('Stock updated', [
                    'product_id' => $product->id,
                    'new_stock' => $product->stock,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            session()->forget('cart');

            DB::commit();
            Log::info('Transaction COMMIT');

            $order->load('items.product', 'user');
            $this->storeInvoicePdf($order);

            event(new OrderPlaced($order));
            Log::info('OrderPlaced event fired');

            return [
                'success' => true,
                'invoice_no' => $order->order_number,
                'order_id' => $order->id, // IMPORTANT FIX
                'date' => now(),
                'items' => $items,
                'grand_total' => $total,
                'user' => auth()->user(),
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Checkout FAILED', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => auth()->id(),
            ]);

            throw $e; // 🔥 IMPORTANT for debug
        }
    }

    private function clearAdminDashboardCache(): void
    {
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['admin'])->flush();

            return;
        }

        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.recent.orders');
        Cache::forget('admin.sales.analytics');
    }

    private function storeInvoicePdf(Order $order): void
    {
        $invoice = [
            'invoice_no' => $order->order_number,
            'date' => $order->created_at,
            'user' => $order->user,
            'items' => $order->items->map(function (OrderItem $item) {
                return [
                    'name' => Arr::get($item->toArray(), 'product.name', 'Product'),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ];
            }),
            'grand_total' => $order->total_amount,
            'shipping_address' => $order->shipping_address,

            'phone' => $order->phone,

            'city' => $order->city,

            'pincode' => $order->pincode,

            'notes' => $order->notes,
        ];

        $pdf = Pdf::loadView('user.invoice.pdf', compact('invoice'));

        Storage::disk('local')->put($order->invoice_storage_path, $pdf->output());
    }
}
