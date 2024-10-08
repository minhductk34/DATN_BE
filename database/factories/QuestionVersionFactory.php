<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionVersion>
 */
class QuestionVersionFactory extends Factory
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
            'Title' => $this->faker->title(),
            'Image_Title' => $this->faker->imageUrl(),
            'Answer_P' => $this->faker->sentence(),
            'Image_P' => $this->faker->imageUrl(),
            'Answer_F1' => $this->faker->sentence(),
            'Image_F1' => $this->faker->imageUrl(),
            'Answer_F2' => $this->faker->sentence(),
            'Image_F2' => $this->faker->imageUrl(),
            'Answer_F3' => $this->faker->sentence(),
            'Image_F3' => $this->faker->imageUrl(),
            'Level' => $this->faker->randomElement(['Easy', 'Medium', 'Difficult']),
            'version' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
