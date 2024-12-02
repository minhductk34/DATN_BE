<?php

namespace Database\Seeders;

use App\Models\Listening;
use App\Models\Listening_question;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListeningQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Listening_question::create( [
                'id'=>$i,
                'listening_id'=>$i,
                'status'=>$faker->boolean(),
                'current_version_id'=>$i,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
