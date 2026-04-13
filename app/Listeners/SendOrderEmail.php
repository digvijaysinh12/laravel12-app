<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class SendOrderEmail
{
    public function handle($event)
    {
        $order = $event->order;

        // Future: Mail::to($order->user->email)->send(...)
        Log::channel('orders')->info('Email sent to customer for Order ID: ' . $order->id);
    }
}