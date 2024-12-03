<?php

namespace Database\Seeders;

use App\Models\Exam_room;
use App\Models\Exam_room_detail;
use App\Models\Exam_session;
use App\Models\Exam_subject;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamRoomDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam_room_detail::create([
                'exam_room_id'=>$i,
                'exam_subject_id'=>$i,
                'exam_session_id'=>$faker->numberBetween(1,6),
                'exam_date'=>now(),
                'exam_end'=>now()->addDay(),
                'create_by'=>$i,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
