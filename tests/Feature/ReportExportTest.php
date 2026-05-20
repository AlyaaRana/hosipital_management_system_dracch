<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_report_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'password']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/reports/export');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure(['status', 'reports']);
    }

    public function test_non_admin_cannot_access_report_export(): void
    {
        $user = User::factory()->create(['role' => 'patient', 'password' => 'password']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/reports/export');

        $response->assertStatus(403);
    }
}
