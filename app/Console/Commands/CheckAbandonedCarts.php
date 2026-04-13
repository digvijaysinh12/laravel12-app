<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Events\CartAbandoned;
use Illuminate\Support\Facades\Log;

class CheckAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cart:abandoned';

    /**
     * The console command description.
     */
    protected $description = 'Check abandoned carts from Redis and dispatch event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking abandoned carts...');

        $userIds = Redis::smembers('cart_users');

        if (empty($userIds)) {
            $this->info('No abandoned carts found.');
            return;
        }

        foreach ($userIds as $userId) {

            $key = 'cart:user_' . $userId;

            $saved = Redis::get($key);

            if (!$saved) {
                continue;
            }

            $data = json_decode($saved, true);

            if (!is_array($data) || !isset($data['last_activity'])) {
                continue;
            }

            if ($data['last_activity'] > now()->subHours(24)->timestamp) {
                continue;
            }

            $user = User::find($userId);

            if (!$user) {
                continue;
            }

            CartAbandoned::dispatch($user);

            Redis::srem('cart_users', $userId);

            Log::channel('customer')->info('Cart abandoned detected', [
                'user_id' => $userId
            ]);

            $this->info("Reminder sent to user {$userId}");
        }

        $this->info('Done.');
    }
}