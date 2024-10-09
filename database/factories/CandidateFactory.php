<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamRoom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
            'Idcode' => $this->faker->unique()->numerify('ID#####'),
            'exam_room_id' => ExamRoom::inRandomOrder()->first()->id,
            'exam_id' => Exam::inRandomOrder()->first()->id,
            'Fullname' => $this->faker->name(),
            'Image' => $this->faker->imageUrl(),
            'DOB' => $this->faker->date(),
            'Address' => $this->faker->optional()->address(),
            'Password' => Hash::make('password'),
            'Email' => $this->faker->unique()->safeEmail(),
            'Status' => $this->faker->randomElement(['Active', 'Inactive']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
