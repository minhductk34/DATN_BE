<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Exam_subject;
use App\Models\Point;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Point::create([
                'exam_subject_id'=>$i,
                'idcode'=>$i,
                'point'=>$faker->randomFloat(2,0,10),
                'number_of_correct_sentences'=>$faker->numberBetween(1,100),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
