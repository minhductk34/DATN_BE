<?php

namespace Database\Factories;

use App\Models\ExamRoom;
use App\Models\ExamSession;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamRoomDetail>
 */
class ExamRoomDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_room_id' => ExamRoom::inRandomOrder()->first()->id,
            'exam_subject_id' => ExamSubject::inRandomOrder()->first()->id,
            'exam_session_id' => ExamSession::inRandomOrder()->first()->id,
            'exam_date' => $this->faker->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
