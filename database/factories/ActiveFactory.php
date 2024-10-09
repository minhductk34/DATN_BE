<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Active>
 */
class ActiveFactory extends Factory
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
            'Active' => $this->faker->randomElement(['Active', 'Inactive']),
            'Reason'=>$this->faker->text(30),
            'admin_id'=>$this->faker->numberBetween(1,60),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
