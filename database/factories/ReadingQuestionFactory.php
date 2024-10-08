<?php

namespace Database\Factories;

use App\Models\Reading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReadingQuestions>
 */
class ReadingQuestionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reading_id' => Reading::inRandomOrder()->first()->id,
            'current_version_id' => $this->faker->numberBetween(1, 10),
            'Status' => $this->faker->randomElement(['true', 'false']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
