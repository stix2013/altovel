<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'images' => [$this->faker->imageUrl(600, 400, 'products'), $this->faker->imageUrl(600, 400, 'products')],
            'specifications' => [$this->faker->word => $this->faker->word, $this->faker->word => $this->faker->word],
            'variations' => [$this->faker->word => [$this->faker->word, $this->faker->word]],
            'stock_status' => $this->faker->randomElement(['in stock', 'out of stock']),
        ];
    }
}
