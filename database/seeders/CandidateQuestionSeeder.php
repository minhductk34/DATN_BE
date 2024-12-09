<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Candidate_question;
use App\Models\Question;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Candidate_question::create([
                'question_id'=>$i,
                'idcode'=>$i,
                'subject_id'=>$i,
                'numerical_order'=>$faker->numberBetween(0,100),
                'answer_P'=>$faker->text(30),
                'answer_Temp'=>$faker->text(30),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
