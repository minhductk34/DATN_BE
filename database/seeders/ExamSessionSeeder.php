<?php

namespace Database\Seeders;

use App\Models\Exam_session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_session::factory()->count(1000)->create();
    }
}
