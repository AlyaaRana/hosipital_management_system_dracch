<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();

        foreach ($doctors as $doctor) {
            Schedule::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => 'Monday',
                ],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '12:00:00',
                ]
            );
        }
    }
}
