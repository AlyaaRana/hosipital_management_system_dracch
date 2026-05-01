<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = \App\Models\Doctor::all();
        foreach ($doctors as $doctor) {
            \App\Models\Schedule::factory()->count(3)->create([
                'doctor_id' => $doctor->id
            ]);
        }
    }
}
