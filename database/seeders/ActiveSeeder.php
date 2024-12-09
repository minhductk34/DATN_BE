<?php

namespace Database\Seeders;

use App\Models\Active;
use App\Models\Admin;
use App\Models\Candidate;
use App\Models\Exam_subject;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            Active::create([
                'exam_subject_id'=>$i,
                'idcode'=>$i,
                'status'=>$faker->boolean(),
                'reason'=>$faker->text(30),
                'admin_id'=>Admin::inRandomOrder()->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
