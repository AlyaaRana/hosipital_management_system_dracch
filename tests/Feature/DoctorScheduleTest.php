<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_index_returns_schedule_data(): void
    {
        $doctor = Doctor::factory()->create();
        Schedule::factory()->for($doctor)->create([
            'day_of_week' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'available_slots' => 4,
        ]);

        $user = User::factory()->create([
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/doctors');

        $response->assertStatus(200)
            ->assertJsonFragment(['hari' => 'Monday'])
            ->assertJsonFragment(['kuota' => 4]);
    }
}
