<?php

namespace Database\Seeders;

use App\Models\Lecturers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LecturersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lecturers::factory()->count(10)->create();
    }
}
