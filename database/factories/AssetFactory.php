<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $symbols = ['BTC', 'ETH', 'XRP', 'ADA', 'SOL', 'DOT', 'BNB'];

        return [
            'symbol' => fake()->randomElement($symbols),
            'amount' => fake()->numberBetween(10, 1500),
            'locked_amount' => 0,
        ];
    }
}
