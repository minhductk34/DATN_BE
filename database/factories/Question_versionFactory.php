<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question_version>
 */
class Question_versionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id'=>Question::inRandomOrder()->first()->id,
            'title'=>$this->faker->text(30),
            'image_title'=>$this->faker->text(30),
            'answer_P'=>$this->faker->text(30),
            'image_P'=>$this->faker->text(30),
            'answer_F1'=>$this->faker->text(30),
            'image_F1'=>$this->faker->text(30),
            'answer_F2'=>$this->faker->text(30),
            'image_F2'=>$this->faker->text(30),
            'answer_F3'=>$this->faker->text(30),
            'image_F3'=>$this->faker->text(30),
            'level'=>$this->faker->randomElement(['easy', 'medium', 'difficult']),
            'version'=>$this->faker->numberBetween(1,10),
            'is_active'=>$this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
