<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\ReadingQuestion;
use App\Models\ReadingQuestionVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReadingQuestionVersion>
 */
class ReadingQuestionVersionFactory extends Factory
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
            'version' => $this->faker->unique()->numberBetween(1, 100),
            'Title' => $this->faker->title(),
            'Answer_P' => $this->faker->optional()->sentence(),
            'Answer_F1' => $this->faker->optional()->sentence(),
            'Answer_F2' => $this->faker->optional()->sentence(),
            'Answer_F3' => $this->faker->optional()->sentence(),
            'Status' => $this->faker->boolean(),
            'Level' => $this->faker->randomElement(['Easy', 'Medium', 'Difficult']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
