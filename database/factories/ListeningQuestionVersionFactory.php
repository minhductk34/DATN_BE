<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListeningQuestionVersion>
 */
class ListeningQuestionVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::inRandomOrder()->first()->id,
            'version' => $this->faker->numberBetween(1, 10),
            'Title' => $this->faker->sentence(),
            'Answer_P' => $this->faker->optional()->word(),
            'Answer_F1' => $this->faker->optional()->word(),
            'Answer_F2' => $this->faker->optional()->word(),
            'Answer_F3' => $this->faker->optional()->word(),
            'Status' => $this->faker->randomElement(['true', 'false']),
            'Level' => $this->faker->randomElement(['Easy', 'Medium', 'Difficult']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
