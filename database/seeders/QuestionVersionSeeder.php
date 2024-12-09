<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Question_version;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Question_version::create([
                'question_id'=> $i,
                'title'=>$faker->text(30),
                'image_title'=>$faker->text(30),
                'answer_P'=>$faker->text(30),
                'image_P'=>$faker->text(30),
                'answer_F1'=>$faker->text(30),
                'image_F1'=>$faker->text(30),
                'answer_F2'=>$faker->text(30),
                'image_F2'=>$faker->text(30),
                'answer_F3'=>$faker->text(30),
                'image_F3'=>$faker->text(30),
                'level'=>$faker->randomElement(['easy', 'medium', 'difficult']),
                'version'=>$faker->numberBetween(1,10),
                'is_active'=>$faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
