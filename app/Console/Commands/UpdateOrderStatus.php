<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order status automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Updating order status... ");

        $orders = Order::where('status','pending')
                    ->where('created_at','<',now()->subHours(24))
                    ->get();

        foreach($orders as $order){
            $order->update(['status' => 'processing']);
        }

        $this->info("Updated " . $orders->count() . " orders.");
    }
}
