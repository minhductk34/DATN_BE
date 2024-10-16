<?php

namespace Database\Seeders;

use App\Models\English_exam_question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnglishExamQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        English_exam_question::factory()->count(1000)->create();
    }
}
