<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'date_of_birth' => '1990-05-01',
            'address' => 'Jalan Merdeka 10',
            'phone' => '081234567890',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'message', 'access_token', 'token_type'])
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'role' => 'patient',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'message', 'access_token', 'token_type', 'role'])
            ->assertJson(['status' => 'success', 'role' => 'patient']);
    }
}
