<?php

namespace Database\Factories;

use App\Models\Listening_question;
use App\Models\Listening_question_version;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listening_question>
 */
class ListeningQuestionFactory extends Factory
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
            'listening_id',
            'status'=>$this->faker->boolean(),
            'current_version_id'=>Listening_question_version::inRandomOrder()->first()->id
        ];
    }
}
