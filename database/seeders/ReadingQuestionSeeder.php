<?php

namespace Database\Seeders;

use App\Models\Reading_question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReadingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reading_question::factory()->count(1000)->create();
    }
}
