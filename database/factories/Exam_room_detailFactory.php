<?php

namespace Database\Factories;

use App\Models\Exam_room;
use App\Models\Exam_session;
use App\Models\Exam_subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam_room_detail>
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
            'exam_room_id'=>Exam_room::inRandomOrder()->first()->id,
            'exam_subject_id'=>Exam_subject::inRandomOrder()->first()->id,
            'exam_session_id'=>Exam_session::inRandomOrder()->first()->id,
            'exam_date'=>$this->faker->date(),
        ];
    }
}
