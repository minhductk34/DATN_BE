<?php

namespace Database\Seeders;

use App\Models\ExamRoomDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamRoomDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExamRoomDetail::factory()->count(10)->create();
    }
}
