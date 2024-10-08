<?php

namespace Database\Seeders;

use App\Models\ExamSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExamSubject::factory()->count(10)->create();
    }
}
