<?php

namespace Database\Seeders;

use App\Models\Exam_content;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_content::factory()->count(1000)->create();
    }
}
