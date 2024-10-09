<?php

namespace Database\Seeders;

use App\Models\ListeningQuestionVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningQuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ListeningQuestionVersion::factory()->count(10)->create();
    }
}
