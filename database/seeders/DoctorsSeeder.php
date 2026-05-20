<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorsSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "doctor{$i}@hospital.com"],
                [
                    'name' => 'dr. ' . fake('id_ID')->name(),
                    'password' => Hash::make('password123'),
                    'role' => 'doctor',
                    'email_verified_at' => now(),
                ]
            );

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => fake('id_ID')->randomElement(['Poli Umum', 'Poli Gigi', 'Poli Anak', 'Poli Jantung']),
                    'phone' => '+628' . fake()->numerify('##########'),
                ]
            );
        }
    }
}
