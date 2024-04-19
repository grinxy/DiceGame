<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // se asigna en seeder aleatoriamente
            'dice1_value' => fake()->numberBetween(1, 6),
            'dice2_value' => fake()->numberBetween(1, 6),
            'sum' => function (array $diceNum) {
                return $diceNum['dice1_value'] + $diceNum['dice2_value'];
            },
            'result' => fake()->randomElement(['won', 'lost']),
        ];
    }
}

