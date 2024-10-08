<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
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
            'Idcode' => Candidate::inRandomOrder()->first()->Idcode,
            'Point' => $this->faker->randomFloat(2, 0, 99.99),
            'Number_of_correct_sentences' => $this->faker->numberBetween(0, 50),
            'TimeStart' => $this->faker->dateTime(),
            'TimeEnd' => $this->faker->dateTime(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
