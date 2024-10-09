<?php

namespace Database\Seeders;

use App\Models\CandidateQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CandidateQuestion::factory()->count(10)->create();
    }
}
