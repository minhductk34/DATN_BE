<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExamFactory extends Factory
{
    protected $model = \App\Models\Exam::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(), // Tạo UUID cho cột 'id'
            'Name' => $this->faker->word(),
            'TimeStart' => $this->faker->dateTimeBetween('now', '+1 week'),
            'TimeEnd' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'Status' => $this->faker->randomElement(['Scheduled', 'Ongoing', 'Completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
