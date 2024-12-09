<?php

namespace Database\Seeders;

use App\Models\Exam_content;
use App\Models\Exam_structure;
use App\Models\Exam_subject;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam_structure::create([
                'exam_subject_id'=>$i,
                'exam_content_id'=>$i,
                'level'=>$faker->randomElement(['easy', 'medium', 'difficult']),
                'quantity'=>$faker->numberBetween(1,100),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
