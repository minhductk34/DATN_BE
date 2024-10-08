<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateQuestion>
 */
class CandidateQuestionFactory extends Factory
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
            'Idcode' => Candidate::inRandomOrder()->first()->Idcode,
            'Numerical_order' => $this->faker->unique()->numberBetween(1, 100),
            'Answer_P' => $this->faker->sentence(),
            'Answer_Pi' => $this->faker->sentence(),
            'Answer_Temp' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
