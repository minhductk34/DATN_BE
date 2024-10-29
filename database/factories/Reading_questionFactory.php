<?php

namespace Database\Factories;

use App\Models\Reading;
use App\Models\Reading_question_version;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reading_question>
 */
class Reading_questionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reading = Reading::inRandomOrder()->first();
        $readingQuestionVersion = Reading_question_version::inRandomOrder()->first();

        return [
            'id' => $this->faker->uuid(),
            'reading_id' => $reading ? $reading->id : null,
            'status' => $this->faker->boolean(),
            'current_version_id' => $readingQuestionVersion ? $readingQuestionVersion->id : null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
