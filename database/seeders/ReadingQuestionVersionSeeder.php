<?php

namespace Database\Seeders;

use App\Models\Reading_question_version;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReadingQuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reading_question_version::factory()->count(1000)->create();
    }
}
