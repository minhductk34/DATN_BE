<?php

namespace Database\Seeders;

use App\Models\Exam_subject_detail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_subject_detail::factory()->count(1000)->create();
    }
}
