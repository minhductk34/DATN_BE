<?php

namespace Database\Seeders;

use App\Models\CandidatesTakeTheEnglishTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidatesTakeTheEnglishTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CandidatesTakeTheEnglishTest::factory()->count(10)->create();
    }
}
