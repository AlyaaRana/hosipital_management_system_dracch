<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_and_download_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => 'patient',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/files/upload', [
                'type' => 'rekam_medis',
                'file' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Upload berhasil!']);

        $fileId = $response->json('data.id');
        $this->assertNotNull($fileId);

        $downloadResponse = $this->actingAs($user, 'sanctum')
            ->get('/api/v1/files/' . $fileId);

        $downloadResponse->assertStatus(200)
            ->assertHeader('content-disposition');
    }

    public function test_user_can_soft_delete_uploaded_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => 'patient',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $uploadResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/files/upload', [
                'type' => 'rekam_medis',
                'file' => UploadedFile::fake()->create('report.pdf', 120, 'application/pdf'),
            ]);

        $fileId = $uploadResponse->json('data.id');

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/files/' . $fileId)
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'message' => 'File berhasil dihapus']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/files/' . $fileId)
            ->assertStatus(404);
    }
}
