<?php

namespace Database\Seeders;

use App\Models\English_exam_question;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnglishExamQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            English_exam_question::create([
                'question_id'=>$i,
                'reading_question_id'=>$i,
                'listening_question_id'=>$i,
                'idcode'=> $i,
                'numerical_order'=>$faker->numberBetween(1,100),
                'answer_P'=>$faker->text(30),
                'answer_Pi'=>$faker->text(30),
                'answer_Temp'=>$faker->text(30),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
