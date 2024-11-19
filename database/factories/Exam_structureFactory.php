<?php

namespace Database\Factories;

use App\Models\Exam_content;
use App\Models\Exam_subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam_structure>
 */
class Exam_structureFactory extends Factory
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
            'exam_content_id'=>Exam_content::inRandomOrder()->first()->id,
            'level'=>$this->faker->randomElement(['easy', 'medium', 'difficult']),
            'quantity'=>$this->faker->numberBetween(1,100),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
