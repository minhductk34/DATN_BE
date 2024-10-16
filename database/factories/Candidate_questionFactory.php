<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate_question>
 */
class Candidate_questionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id'=>Question::inRandomOrder()->first()->id,
            'idcode'=>Candidate::inRandomOrder()->first()->id,
            'numerical_order'=>$this->faker->numberBetween(0,100),
            'answer_P'=>$this->faker->text(30),
            'answer_Pi'=>$this->faker->text(30),
            'answer_Temp'=>$this->faker->text(30),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
