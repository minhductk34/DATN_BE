<?php

namespace Database\Seeders;

use App\Models\Exam_subject;
use App\Models\Exam_subject_detail;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam_subject_detail::create([
                'exam_subject_id'=> $i,
                'quantity'=>$faker->numberBetween(1,45),
                'time'=>$faker->numberBetween(1,120),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
