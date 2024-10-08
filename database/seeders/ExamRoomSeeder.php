<?php

namespace Database\Seeders;

use App\Models\ExamRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExamRoom::factory()->count(10)->create();
    }
}
