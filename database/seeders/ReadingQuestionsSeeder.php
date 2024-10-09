<?php

namespace Database\Seeders;

use App\Models\ReadingQuestion;
use App\Models\ReadingQuestionVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReadingQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReadingQuestion::factory()->count(10)->create();

    }
}
