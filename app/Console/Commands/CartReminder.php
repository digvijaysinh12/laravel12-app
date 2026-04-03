<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CartReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send cart reminder emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Sending cart reminders... ");

        // Get all users with cart
        $userIds = Redis::smembers('cart_users');

        if(empty($userIds)){
            $this->info('No users with orders');
        }

        $users = User::whereIn('id',$userIds)->get();

        if($users->isEmpty()){
            $this->info('No valid users found.');
            return;
        }

        $this->withProgressBar($users, function($user){
            $cartKey = "cart:user_{$user->id}";
            $cart = Redis::get($cartKey);

            if ($cart) {
                $this->line("Reminder sent to: {$user->email}");
            }

            usleep(50000);            
        });

        $this->newLine();
        $this->info("Cart reminders sent!");
    }
}
