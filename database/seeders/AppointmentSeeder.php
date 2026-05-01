<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = \App\Models\Patient::all();
        $doctors = \App\Models\Doctor::all();
        $schedules = \App\Models\Schedule::all();

        for ($i = 0; $i < 200; $i++) {
            \App\Models\Appointment::factory()->create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->random()->id,
                'schedule_id' => $schedules->random()->id,
            ]);
        }
    }
}
