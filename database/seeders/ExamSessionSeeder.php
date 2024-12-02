<?php

namespace Database\Seeders;

use App\Models\Exam_session;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ca 1',
                'time_start' => '07:15:00',
                'time_end' => '09:15:00',
            ],
            [
                'name' => 'Ca 2',
                'time_start' => '09:30:00',
                'time_end' => '11:30:00',
            ],
            [
                'name' => 'Ca 3',
                'time_start' => '13:15:00',
                'time_end' => '15:15:00',
            ],
            [
                'name' => 'Ca 4',
                'time_start' => '15:30:00',
                'time_end' => '17:30:00',
            ],
            [
                'name' => 'Ca 5',
                'time_start' => '17:45:00',
                'time_end' => '19:45:00',
            ],
            [
                'name' => 'Ca 6',
                'time_start' => '20:00:00',
                'time_end' => '22:00:00',
            ]
        ];

        foreach ($data as $session) {
            Exam_session::create([
                'name' => $session['name'],
                'time_start' => $session['time_start'],
                'time_end' => $session['time_end'],
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
