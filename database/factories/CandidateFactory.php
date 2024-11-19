<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Exam_room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idcode'=>$this->faker->uuid(),
            'exam_room_id'=>Exam_room::inRandomOrder()->first()->id,
            'exam_id'=>Exam::inRandomOrder()->first()->id,
            'name'=>$this->faker->name(),
            'image'=>$this->faker->imageUrl(),
            'dob'=>$this->faker->date(),
            'address'=>$this->faker->address(),
            'email'=>$this->faker->unique()->email(),
            'status'=>$this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
