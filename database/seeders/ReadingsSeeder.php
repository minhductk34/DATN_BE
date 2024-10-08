<?php

namespace Database\Seeders;

use App\Models\Reading;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReadingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reading::factory()->count(10)->create();
    }
}
