<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Listening;
use App\Models\Listening_question;
use App\Models\Question;
use App\Models\Reading;
use App\Models\Reading_question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\English_exam_question>
 */
class English_exam_questionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $Question = Question::inRandomOrder()->first();
        $Reading_question = Reading_question::inRandomOrder()->first();
        $Listening_question = Listening_question::inRandomOrder()->first();
        $Candidate = Candidate::inRandomOrder()->first();
        return [
            'question_id'=>$Question ? $Question->id : null,
            'reading_question_id'=>$Reading_question ? $Reading_question->id : null,
            'listening_question_id'=>$Listening_question ? $Listening_question->id : null,
            'idcode'=> $Candidate ? $Candidate->idcode : null,
            'numerical_order'=>$this->faker->numberBetween(1,100),
            'answer_P'=>$this->faker->text(30),
            'answer_Pi'=>$this->faker->text(30),
            'answer_Temp'=>$this->faker->text(30),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
