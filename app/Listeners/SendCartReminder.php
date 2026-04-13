<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCartReminder implements ShouldQueue
{
    public function handle(CartAbandoned $event)
    {
        \Log::channel('customer')->info('Cart abandoned reminder scheduled', [
            'user_id' => $event->user->id,
        ]);
    }
}