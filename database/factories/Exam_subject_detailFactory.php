<?php

namespace Database\Factories;

use App\Models\Exam_subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam_subject_detail>
 */
class ExamSubjectDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_subject_id'=>Exam_subject::inRandomOrder()->first()->id,
            'quantity'=>$this->faker->numberBetween(1,100),
            'time'=>$this->faker->numberBetween(1,100),
        ];
    }
}
