<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $user = User::firstOrCreate(
                ['email' => "patient{$i}@hospital.com"],
                [
                    'name' => fake('id_ID')->name(),
                    'password' => Hash::make('password123'),
                    'role' => 'patient',
                    'email_verified_at' => now(),
                ]
            );

            Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => fake('id_ID')->dateTimeBetween('-60 years', '-10 years')->format('Y-m-d'),
                    'address' => fake('id_ID')->address(),
                    'phone' => '+628' . fake()->numerify('##########'),
                ]
            );
        }
    }
}
