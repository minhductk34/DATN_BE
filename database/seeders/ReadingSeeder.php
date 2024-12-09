<?php

namespace Database\Seeders;

use App\Models\Exam_content;
use App\Models\Reading;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Reading::create([
                'id'=>$i,
                'exam_content_id'=>$i,
                'title'=>$faker->text(30),
                'status'=>$faker->boolean(),
                'level'=>$faker->randomElement(['easy', 'medium', 'difficult']),
                'image'=>$faker->imageUrl(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
