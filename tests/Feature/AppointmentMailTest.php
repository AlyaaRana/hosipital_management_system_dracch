<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentStatusChanged;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_receives_confirmation_email_when_appointment_created(): void
    {
        Mail::fake();

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

        $response->assertStatus(201);

        Mail::assertSent(AppointmentConfirmation::class, function ($mail) use ($patientUser) {
            return $mail->hasTo($patientUser->email);
        });
    }

    public function test_patient_receives_status_change_email_when_appointment_status_updated(): void
    {
        Mail::fake();

        $doctorUser = User::factory()->create([
            'role' => 'doctor',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $doctor = Doctor::factory()->create(['user_id' => $doctorUser->id]);
        $patientUser = User::factory()->create([
            'role' => 'patient',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
        $schedule = Schedule::factory()->for($doctor)->create(['available_slots' => 1]);

        $this->actingAs($patientUser, 'sanctum')
            ->postJson('/api/v1/appointments', [
                'doctor_id' => $doctor->id,
                'schedule_id' => $schedule->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'complaint' => 'Sakit kepala',
            ])
            ->assertStatus(201);

        $appointment = $patient->appointments()->first();

        $response = $this->actingAs($doctorUser, 'sanctum')
            ->putJson('/api/v1/appointments/' . $appointment->id, [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200);

        Mail::assertSent(AppointmentStatusChanged::class, function ($mail) use ($patientUser) {
            return $mail->hasTo($patientUser->email);
        });
    }
}
