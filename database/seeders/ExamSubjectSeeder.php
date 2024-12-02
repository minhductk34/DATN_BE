<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Exam_subject;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam_subject::create([
                'id'=>$i,
                'exam_id'=>$i,
                'name'=>$faker->name(),
                'create_by'=>$i,
                'status'=>$faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
