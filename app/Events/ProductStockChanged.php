<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $productId;
    public $stock;

    public function __construct($productId, $stock)
    {
        $this->productId = $productId;
        $this->stock = $stock;
    }

    // Public channel
    public function broadcastOn()
    {
        return new Channel('product.' . $this->productId);
    }

    public function broadcastAs()
    {
        return 'stock.updated';
    }
}