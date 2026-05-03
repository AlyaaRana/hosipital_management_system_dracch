<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'specialization' => fake()->randomElement(['Umum', 'Gigi', 'Anak', 'Kandungan', 'Penyakit Dalam']), // kategori dokter
            'phone' => fake('id_ID')->phoneNumber(),
            'photo' => 'doctors/default.jpg',
        ];
    }
}
