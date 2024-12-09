<?php

namespace Database\Seeders;

use App\Models\Exam_content;
use App\Models\Exam_subject;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Exam_content::create([
                'id'=>$i,
                'exam_subject_id'=>$i,
                'title'=>$faker->text('30'),
                'status'=>$faker->boolean(),
                'url_listening'=>$faker->url(),
                'description'=>$faker->text(200),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
