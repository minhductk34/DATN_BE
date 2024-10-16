<?php

namespace Database\Factories;

use App\Models\Reading;
use App\Models\Reading_question_version;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reading_question>
 */
class ReadingQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'=>$this->faker->uuid(),
            'reading_id'=>Reading::inRandomOrder()->first()->id,
            'status'=>$this->faker->boolean(),
            'current_version_id'=>Reading_question_version::inRandomOrder()->first()->id
        ];
    }
}
