<?php

namespace Database\Factories;

use App\Models\Exam_content;
use App\Models\Question_version;
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
        $questionVersion = Question_version::inRandomOrder()->first();
        $examContent = Exam_content::inRandomOrder()->first();

        return [
            'id' => $this->faker->uuid(),
            'current_version_id' => $questionVersion ? $questionVersion->id : null,
            'exam_content_id' => $examContent ? $examContent->id : null,
            'status' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
