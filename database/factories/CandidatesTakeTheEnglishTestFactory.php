<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Listening;
use App\Models\Question;
use App\Models\Reading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidatesTakeTheEnglishTest>
 */
class CandidatesTakeTheEnglishTestFactory extends Factory
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
            'reading_id' => Reading::inRandomOrder()->first()->id,
            'listening_id' => Listening::inRandomOrder()->first()->id,
            'Idcode' => Candidate::inRandomOrder()->first()->Idcode,
            'Numerical_order' => $this->faker->unique()->numberBetween(1, 100),
            'Answer_P' => $this->faker->sentence(),
            'Answer_Pi' => $this->faker->sentence(),
            'Answer_Temp' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
