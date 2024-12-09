<?php

namespace Database\Seeders;

use App\Models\Listening_question;
use App\Models\Listening_question_version;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningQuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Listening_question_version::create([
                'listening_question_id'=>$i,
                'version'=>$faker->numberBetween(1,10),
                'title'=>$faker->text(30),
                'answer_P'=>$faker->text(30),
                'answer_F1'=>$faker->text(30),
                'answer_F2'=>$faker->text(30),
                'answer_F3'=>$faker->text(30),
                'status'=>$faker->boolean,
                'level'=>$faker->randomElement(['easy', 'medium', 'difficult']),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
