<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderEmail
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        $order->loadMissing('items.product', 'user');

        if (! $order->user?->email) {
            Log::channel('orders')->error('User email missing', [
                'order_id' => $order->id,
            ]);

            return;
        }

        try {
            Mail::to($order->user)
                ->queue(new OrderConfirmation($order));

            Log::channel('orders')->info('Order confirmation queued successfully', [
                'order_id' => $order->id,
                'email' => $order->user->email,
            ]);

        } catch (\Exception $e) {
            Log::channel('orders')->error('Failed to queue order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
