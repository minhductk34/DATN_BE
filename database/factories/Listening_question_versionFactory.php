<?php

namespace Database\Factories;

use App\Models\Listening_question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listening_question_version>
 */
class Listening_question_versionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listening_question_id'=>Listening_question::inRandomOrder()->first()->id,
            'version'=>$this->faker->numberBetween(1,10),
            'title'=>$this->faker->text(30),
            'answer_P'=>$this->faker->text(30),
            'answer_F1'=>$this->faker->text(30),
            'answer_F2'=>$this->faker->text(30),
            'answer_F3'=>$this->faker->text(30),
            'status'=>$this->faker->boolean,
            'level'=>$this->faker->randomElement(['easy', 'medium', 'difficult']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
