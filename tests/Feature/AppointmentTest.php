<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_create_appointment_and_schedule_slots_decrement(): void
    {
        $patientUser = User::factory()->create([
            'role' => 'patient',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
        $doctor = Doctor::factory()->create();
        $schedule = Schedule::factory()->for($doctor)->create(['available_slots' => 2]);

        $response = $this->actingAs($patientUser, 'sanctum')
            ->postJson('/api/v1/appointments', [
                'doctor_id' => $doctor->id,
                'schedule_id' => $schedule->id,
                'appointment_date' => now()->addDays(1)->toDateString(),
                'complaint' => 'Saya demam dan batuk',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Janji temu berhasil dibuat!']);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
        ]);

        $this->assertSame(1, Schedule::find($schedule->id)->available_slots);
    }

    public function test_patient_cannot_create_appointment_when_schedule_is_full(): void
    {
        $patientUser = User::factory()->create([
            'role' => 'patient',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
        $doctor = Doctor::factory()->create();
        $schedule = Schedule::factory()->for($doctor)->create(['available_slots' => 0]);

        $response = $this->actingAs($patientUser, 'sanctum')
            ->postJson('/api/v1/appointments', [
                'doctor_id' => $doctor->id,
                'schedule_id' => $schedule->id,
                'appointment_date' => now()->addDays(1)->toDateString(),
                'complaint' => 'Tidak bisa makan',
            ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Maaf, kuota jadwal dokter ini sudah penuh.']);
    }
}
