<?php

namespace Tests\Feature;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SchedulerCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_appointments_sends_reminders_for_tomorrow_confirmed_appointments(): void
    {
        Mail::fake();

        $doctor = Doctor::factory()->create();
        $patientUser = User::factory()->create([
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
        $schedule = Schedule::factory()->for($doctor)->create(['available_slots' => 1]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'status' => 'confirmed',
            'complaint' => 'Tes pengingat',
        ]);

        $this->artisan('hospital:process-appointments')->assertExitCode(0);

        Mail::assertSent(AppointmentReminder::class, function ($mail) use ($patientUser) {
            return $mail->hasTo($patientUser->email);
        });
    }
}
