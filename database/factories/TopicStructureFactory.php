<?php

namespace Database\Factories;

use App\Models\ExamContent;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicStructure>
 */
class TopicStructureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_content_id' => ExamContent::inRandomOrder()->first()->id,
            'exam_subject_id' => ExamSubject::inRandomOrder()->first()->id,
            'Level' => $this->faker->randomElement(['Easy', 'Medium', 'Difficult']),
            'Quality' => $this->faker->numberBetween(1, 60),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
