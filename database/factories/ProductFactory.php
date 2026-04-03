<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 5000),
            'description' => $this->faker->sentence(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? 1,
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => 'default.jpg'
        ];
    }
}
