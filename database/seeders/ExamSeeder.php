<?php

namespace Database\Seeders;

use App\Models\Exam;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam::create([
                'id'=>$i,
                'name'=>$faker->name(),
                'time_start' => now()->subDay(),
                'time_end' => now()->addDay(),
                'status' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
