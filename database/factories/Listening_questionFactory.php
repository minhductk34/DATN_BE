<?php

namespace Database\Factories;

use App\Models\Listening;
use App\Models\Listening_question;
use App\Models\Listening_question_version;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listening_question>
 */
class Listening_questionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listening = Listening::inRandomOrder()->first();
        $Listening_question_version = Listening_question_version::inRandomOrder()->first();
        return [
            'id'=>$this->faker->uuid(),
            'listening_id'=>$listening ? $listening->id : null,
            'status'=>$this->faker->boolean(),
            'current_version_id'=>$Listening_question_version ? $Listening_question_version->id : null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
