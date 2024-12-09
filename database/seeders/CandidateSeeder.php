<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Candidate::create([
                'idcode'=>$i,
                'exam_room_id'=>$i,
                'exam_id'=>$i,
                'name'=>$faker->name(),
                'image'=>$faker->imageUrl(),
                'dob'=>$faker->date(),
                'address'=>$faker->address(),
                'email'=>'user'.$i.'@gmail.com' ,
                'status'=>$faker->boolean(),
                'create_by'=>$i,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
