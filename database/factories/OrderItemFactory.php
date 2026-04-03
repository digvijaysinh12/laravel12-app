<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'order_id' => null, // set later
        'product_id' => $product->id ?? 1,
        'quantity' => $this->faker->numberBetween(1, 3),
        'price' => $product->price ?? 100,
        ];
    }
}
