<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Listening;
use App\Models\Question;
use App\Models\Reading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\English_exam_question>
 */
class EnglishExamQuestionFactory extends Factory
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
            'reading_id'=>Reading::inRandomOrder()->first()->id,
            'listening_id'=>Listening::inRandomOrder()->first()->id,
            'idcode'=>Candidate::inRandomOrder()->first()->id,
            'numerical_order'=>$this->faker->numberBetween(1,100),
            'answer_P'=>$this->faker->text(30),
            'answer_Pi'=>$this->faker->text(30),
            'answer_Temp'=>$this->faker->text(30),
        ];
    }
}
