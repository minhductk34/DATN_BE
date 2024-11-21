<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Password;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidate = Candidate::all();
//        Password::factory()->count(1000)->create();
        foreach ($candidate as $value) {
            Password::create(['idcode' => $value->idcode,
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,]);
        }

    }
}
