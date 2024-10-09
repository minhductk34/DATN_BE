<?php

namespace Database\Seeders;

use App\Models\Listening;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Listening::factory()->count(10)->create();
    }
}
