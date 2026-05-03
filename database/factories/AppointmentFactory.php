<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'patient_id' => null, 
        'doctor_id' => null,
        'schedule_id' => null,
        'appointment_date' => fake()->dateTimeBetween('now', '+1 month'), 
        'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        'complaint' => fake('id_ID')->paragraph(1),
    ];
    }
}
