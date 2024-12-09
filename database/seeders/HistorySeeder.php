<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Exam_subject;
use App\Models\History;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 501; $i++) {
            History::create([
                'exam_subject_id'=>$i,
                'idcode'=>$i,
                'answer'=>$faker->text(),
                'time'=>$faker->dateTime(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
