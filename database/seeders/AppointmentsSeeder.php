<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class AppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $schedules = Schedule::with('doctor')->get();

        if ($patients->isEmpty() || $schedules->isEmpty()) {
            return;
        }

        $currentAppointmentsCount = Appointment::count();
        if ($currentAppointmentsCount < 200) {
            $needed = 200 - $currentAppointmentsCount;

            for ($i = 0; $i < $needed; $i++) {
                $schedule = $schedules->random();
                $patient = $patients->random();

                Appointment::factory()->create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $schedule->doctor_id,
                    'schedule_id' => $schedule->id,
                ]);
            }
        }

        $completedAppointments = Appointment::where('status', 'completed')->get();
        $currentRecordsCount = MedicalRecord::count();

        if ($currentRecordsCount < 100 && $completedAppointments->isNotEmpty()) {
            $neededRecords = 100 - $currentRecordsCount;
            $limit = min($neededRecords, $completedAppointments->count());

            for ($i = 0; $i < $limit; $i++) {
                MedicalRecord::firstOrCreate(
                    ['appointment_id' => $completedAppointments[$i]->id],
                    [
                        'doctor_id' => $completedAppointments[$i]->doctor_id,
                        'diagnosis' => fake('id_ID')->sentence(4),
                        'prescription' => fake('id_ID')->randomElement(['Paracetamol 500mg', 'Amoxicillin 500mg', 'Ibuprofen 400mg']) . ' (3x1)',
                        'notes' => fake('id_ID')->paragraph(),
                    ]
                );
            }
        }
    }
}
