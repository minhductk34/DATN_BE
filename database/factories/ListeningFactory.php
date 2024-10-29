<?php

namespace Database\Factories;

use App\Models\Exam_content;
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
            'id'=>$this->faker->uuid(),
            'exam_content_id'=>Exam_content::inRandomOrder()->first()->id,
            'audio'=>$this->faker->text(30),
            'status'=>$this->faker->boolean(),
            'level'=>$this->faker->randomElement(['easy', 'medium', 'difficult']),
            'name'=>$this->faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
