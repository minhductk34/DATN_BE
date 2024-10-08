<?php

namespace Database\Seeders;

use App\Models\TopicStructure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TopicStructure::factory()->count(10)->create();
    }
}
