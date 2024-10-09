<?php

namespace Database\Seeders;

use App\Models\QuestionVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestionVersion::factory()->count(10)->create();
    }
}
