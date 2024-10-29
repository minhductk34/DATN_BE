<?php

namespace Database\Seeders;

use App\Models\Exam_subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_subject::factory()->count(1000)->create();
    }
}
