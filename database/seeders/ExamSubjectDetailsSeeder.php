<?php

namespace Database\Seeders;

use App\Models\ExamSubjectDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExamSubjectDetails::factory()->count(10)->create();
    }
}
