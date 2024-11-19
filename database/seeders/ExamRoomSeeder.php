<?php

namespace Database\Seeders;

use App\Models\Exam_room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_room::factory()->count(1000)->create();
    }
}
