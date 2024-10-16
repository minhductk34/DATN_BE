<?php

namespace Database\Seeders;

use App\Models\Candidate_question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidate_question::factory()->count(1000)->create();
    }
}
