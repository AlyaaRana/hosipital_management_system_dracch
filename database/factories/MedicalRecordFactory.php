<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'appointment_id' => null, 
            'doctor_id' => null,
            
            'diagnosis' => fake('id_ID')->sentence(3),
            'prescription' => fake()->randomElement([
                'Paracetamol 500mg (3x1)', 
                'Amoxicillin 500mg (2x1)', 
                'Ibuprofen 400mg (setelah makan)'
            ]),
            'notes' => fake('id_ID')->paragraph(),
        ];
    }
}
