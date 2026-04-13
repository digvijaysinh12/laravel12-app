<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class UpdateInventory
{
    public function handle($event)
    {
        $order = $event->order;

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product) {
                $product->decrement('stock', $item->quantity);
            }
        }

        Log::channel('orders')->info('Inventory updated for Order ID: ' . $order->id);
    }
}