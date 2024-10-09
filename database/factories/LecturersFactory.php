<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lecturers>
 */
class LecturersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Idcode' => $this->faker->unique()->uuid(),
            'Fullname' => $this->faker->name(),
            'Profile' => $this->faker->imageUrl(),
            'Email' => $this->faker->unique()->safeEmail(),
            'Status' => $this->faker->randomElement(['Active', 'Inactive']),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
