<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ensure admin user exists
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        User::factory(10)->create();

        Category::factory(5)->create();

        Product::factory(30)->create();

        $orders = Order::factory(20)->create();

        foreach ($orders as $order) {

            $items = OrderItem::factory(rand(1, 4))->make();

            $total = 0;

            foreach ($items as $item) {
                $item->order_id = $order->id;
                $item->save();

                $total += $item->price * $item->quantity;
            }

            $order->update([
                'total_amount' => $total
            ]);
        }
    }
}
