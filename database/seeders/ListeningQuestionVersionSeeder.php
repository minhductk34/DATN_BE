<?php

namespace Database\Seeders;

use App\Models\Listening_question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningQuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Listening_question::factory()->count(1000)->create();
    }
}
