<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 101; $i++) {
            Lecturer::create([
                'idcode' => $i,
                'name' => $faker->name(),
                'profile' => $faker->imageUrl(),
                'email' => "giangvien{$i}@gmail.com",
                'status' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
