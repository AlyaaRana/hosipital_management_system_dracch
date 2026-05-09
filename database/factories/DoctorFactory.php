<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'doctor']),
            'specialization' => fake('id_ID')->randomElement(['Dokter Umum', 'Spesialis Anak', 'Spesialis Jantung', 'Spesialis Bedah', 'Spesialis Saraf']),
            'phone' => '+628' . fake()->numerify('##########'),
        ];
    }
}
