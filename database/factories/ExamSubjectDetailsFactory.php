<?php

namespace Database\Factories;

use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSubjectDetails>
 */
class ExamSubjectDetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_subject_id' => ExamSubject::inRandomOrder()->first()->id,
            'Quantity' => $this->faker->numberBetween(1, 100),
            'Time' => $this->faker->numberBetween(1, 120),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
