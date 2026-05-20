<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
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
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'address' => fake('id_ID')->address(),
            'phone' => fake('id_ID')->phoneNumber(),
        ];
    }
}
