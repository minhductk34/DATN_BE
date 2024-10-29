<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Candidate;
use App\Models\Exam_subject;
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
            'exam_subject_id'=>Exam_subject::inRandomOrder()->first()->id,
            'idcode'=>Candidate::inRandomOrder()->first()->idcode,
            'active'=>$this->faker->randomElement(['active','inactive']),
            'reason'=>$this->faker->text(30),
            'admin_id'=>Admin::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
