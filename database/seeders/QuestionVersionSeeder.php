<?php

namespace Database\Seeders;

use App\Models\Question_version;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question_version::factory()->count(1000)->create();
    }
}
