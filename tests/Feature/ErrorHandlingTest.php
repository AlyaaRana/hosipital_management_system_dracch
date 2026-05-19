<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_json_404_for_missing_route(): void
    {
        $response = $this->getJson('/api/v1/invalid-endpoint');

        $response->assertStatus(404)
            ->assertJson(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }

    public function test_register_returns_json_422_when_validation_fails(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJson(['status' => 'error', 'message' => 'Validasi gagal']);
    }
}
