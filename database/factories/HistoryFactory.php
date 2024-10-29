<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Exam_subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
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
            'idcode'=>Candidate::inRandomOrder()->first()->id,
            'answer'=>$this->faker->text(),
            'time'=>$this->faker->dateTime(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
