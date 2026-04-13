<?php

namespace App\Console\Commands;

use App\Services\Admin\OrderService;
use Illuminate\Console\Command;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order status automatically';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService): int
    {
        $this->info('Updating overdue order statuses...');

        $updatedCount = $orderService->promotePendingOrdersToProcessing();

        $this->info("Updated {$updatedCount} orders.");

        return self::SUCCESS;
    }
}
