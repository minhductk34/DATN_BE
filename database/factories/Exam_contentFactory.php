<?php

namespace Database\Factories;

use App\Models\Exam_subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam_content>
 */
class Exam_contentFactory extends Factory
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
            'exam_subject_id'=>Exam_subject::inRandomOrder()->first()->id,
            'title'=>$this->faker->text('30'),
            'status'=>$this->faker->boolean(),
            'url_listening'=>$this->faker->url(),
            'description'=>$this->faker->text(200),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
