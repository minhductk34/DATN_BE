<?php

namespace Database\Factories;

use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listening>
 */
class ListeningFactory extends Factory
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
            'exam_content_id' => ExamContent::inRandomOrder()->first()->id,
            'Name' => $this->faker->name(),
            'Audio' => $this->faker->randomElement(['audio/audio1.mp3', 'audio/audio2.mp3', 'audio/audio3.mp3']),
            'Status' => $this->faker->randomElement(['true', 'false']),
            'Level' => $this->faker->randomElement(['Easy', 'Medium', 'Difficult']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
