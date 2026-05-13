<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Mail\OrderShippedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderShipped $event): void
    {
        $order = $event->order;

        $order->loadMissing('user', 'items.product');

        if (! $order->user?->email) {
            Log::channel('orders')->error('Order shipped email missing', [
                'order_id' => $order->id,
            ]);

            return;
        }

        try {
            Mail::to($order->user->email)
                ->queue(new OrderShippedMail($order));

            Log::channel('orders')->info('Order shipped email queued', [
                'order_id' => $order->id,
                'email' => $order->user->email,
            ]);

        } catch (\Throwable $e) {
            Log::channel('orders')->error('Failed to queue shipped email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}