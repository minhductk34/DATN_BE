<?php

namespace Database\Factories;

use App\Models\Listening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListeningQuestions>
 */
class ListeningQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'current_version_id' => $this->faker->numberBetween(1, 10),
            'listening_id' => Listening::inRandomOrder()->first()->id,
            'Status' => $this->faker->randomElement(['true', 'false']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
