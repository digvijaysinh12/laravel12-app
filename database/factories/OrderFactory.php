<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => \App\Models\User::inRandomOrder()->first()->id ?? 1,
        'order_number' => 'ORD-' . strtoupper(uniqid()),
        'total_amount' => 0, // will update later
        'status' => $this->faker->randomElement([
            'pending',
            'confirmed',
            'shipped',
            'delivered',
            'cancelled'
        ]),
        'payment_method' => 'cod',
        'payment_status' => 'paid',
        'shipping_address' => $this->faker->address(),
        'phone' => $this->faker->phoneNumber(),
        ];
    }
}
