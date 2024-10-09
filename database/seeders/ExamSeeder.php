<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 bản ghi mẫu
        Exam::factory()->count(10)->create();
    }
}
