<?php

namespace Database\Seeders;

use App\Models\Exam_room_detail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamRoomDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_room_detail::factory()->count(1000)->create();
    }
}
