<?php

namespace Database\Seeders;

use App\Models\ListeningQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ListeningQuestion::factory()->count(10)->create();
    }
}
