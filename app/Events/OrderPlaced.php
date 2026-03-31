<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $total;

    public function __construct($user, $total)
    {
        $this->user = $user;
        $this->total = $total;
    }

    public function broadcastOn()
    {
        return new Channel('admin.orders');
    }
}