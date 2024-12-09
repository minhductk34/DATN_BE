<?php

namespace Database\Seeders;

use App\Models\Exam_content;
use App\Models\Listening;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Listening::create([
                'id'=>$i,
                'exam_content_id'=>$i,
                'audio'=>$faker->text(30),
                'status'=>$faker->boolean(),
                'level'=>$faker->randomElement(['easy', 'medium', 'difficult']),
                'name'=>$faker->name(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
