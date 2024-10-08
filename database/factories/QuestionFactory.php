<?php

namespace Database\Factories;

use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
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
            'current_version_id' => 1,
            'exam_content_id' => ExamContent::inRandomOrder()->first()->id,
            'Status' => $this->faker->randomElement(['true', 'false']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
