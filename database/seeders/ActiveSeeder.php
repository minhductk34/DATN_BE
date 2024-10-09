<?php

namespace Database\Seeders;

use App\Models\Active;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Active::factory()->count(10)->create();
    }
}
