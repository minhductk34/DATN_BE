<?php

namespace Database\Seeders;

use App\Models\ReadingQuestionVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedingQuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReadingQuestionVersion::factory()->count(10)->create();
    }
}
