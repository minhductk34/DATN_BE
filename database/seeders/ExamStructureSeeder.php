<?php

namespace Database\Seeders;

use App\Models\Exam_structure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam_structure::factory()->count(1000)->create();
    }
}
