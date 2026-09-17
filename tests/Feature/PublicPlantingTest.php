<?php

namespace Tests\Feature;

use App\Models\PlantingAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPlantingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_form_requires_a_valid_access_token(): void
    {
        $this->get(route('planting.form', ['token' => 'invalid-token']))->assertNotFound();
    }

    public function test_participant_can_submit_a_planting_record_with_a_valid_token(): void
    {
        Storage::fake('public');
        $token = 'BALI-EKATARU-2026';
        $accessToken = PlantingAccessToken::create([
            'token_hash' => hash('sha256', $token),
            'label' => 'Form demo Bali',
        ]);

        $this->get(route('planting.form', ['token' => $token]))
            ->assertOk()
            ->assertSee('Catat penanaman pohon');

        $this->post(route('planting.store', ['token' => $token]), [
            'name' => 'Made Santika',
            'phone' => '081234567890',
            'email' => 'made@example.com',
            'organization' => 'Desa Adat Sanur',
            'job_title' => 'Relawan',
            'planted_at' => now()->toDateString(),
            'plant_type' => 'Mangrove',
            'tree_count' => 25,
            'latitude' => '-8.6905000',
            'longitude' => '115.2126000',
            'location_name' => 'Tahura Ngurah Rai',
            'photo' => UploadedFile::fake()->create('mangrove.jpg', 100, 'image/jpeg'),
            'consent' => '1',
        ])->assertRedirect(route('planting.form', ['token' => $token]));

        $this->assertDatabaseHas('planting_records', [
            'planting_access_token_id' => $accessToken->id,
            'name' => 'Made Santika',
            'plant_type' => 'Mangrove',
            'tree_count' => 25,
        ]);
        $this->assertNotNull($accessToken->fresh()->last_used_at);
    }
}
