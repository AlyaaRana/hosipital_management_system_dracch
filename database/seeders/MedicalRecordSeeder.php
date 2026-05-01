<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $appointments = \App\Models\Appointment::all();

        if ($appointments->count() > 0) {
            for ($i = 0; $i < 100; $i++) {
                $appt = $appointments->random();
                \App\Models\MedicalRecord::factory()->create([
                    'appointment_id' => $appt->id,
                    'doctor_id' => $appt->doctor_id, 
                ]);
            }
        }
    }
}
