<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $productId;
    public int $stock;

    public function __construct(int $productId, int $stock)
    {
        $this->productId = $productId;
        $this->stock = $stock;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('product.'.$this->productId);
    }

    public function broadcastAs(): string
    {
        return 'stock.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'productId' => $this->productId,
            'stock' => $this->stock,
        ];
    }
}
