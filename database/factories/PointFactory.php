<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Exam_subject;
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
            'exam_subject_id'=>Exam_subject::inRandomOrder()->first()->id,
            'idcode'=>Candidate::inRandomOrder()->first()->id,
            'point'=>$this->faker->randomFloat(2,0,10),
            'number_of_correct_sentences'=>$this->faker->numberBetween(1,100),
            'time_start' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'time_end' => $this->faker->dateTimeBetween('now', '+1 month'),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
