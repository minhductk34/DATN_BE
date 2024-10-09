<?php

namespace Database\Factories;

use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamContent>
 */
class ExamContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'exam_subject_id' => ExamSubject::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(),
            'Status' => $this->faker->randomElement(['true', 'false']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
