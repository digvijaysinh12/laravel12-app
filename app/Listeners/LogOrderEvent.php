<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class LogOrderEvent
{
    public function handle($event)
    {
        $order = $event->order;

        Log::channel('orders')->info('Event: ' . get_class($event) . ' triggered for Order ID: ' . $order->id);
    }
}