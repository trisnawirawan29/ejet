<?php

namespace Tests\Feature;

use App\Models\PlantingAccessToken;
use App\Models\PlantingRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantingAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_records_for_a_specific_planting_token(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $admin->roles()->attach($role);
        $token = PlantingAccessToken::create([
            'token_hash' => hash('sha256', 'token-one'),
            'label' => 'Kegiatan Sanur',
            'location_name' => 'Tahura Ngurah Rai',
            'latitude' => '-8.6905000',
            'longitude' => '115.2126000',
        ]);
        $otherToken = PlantingAccessToken::create([
            'token_hash' => hash('sha256', 'token-two'),
            'label' => 'Kegiatan lain',
        ]);

        PlantingRecord::create($this->recordData($token, 'Made Santika', 'Mangrove'));
        PlantingRecord::create($this->recordData($otherToken, 'Wayan Lain', 'Mahoni'));

        $response = $this->actingAs($admin)->get(route('admin.planting-tokens.records', $token));

        $response->assertOk()
            ->assertSee('Kegiatan Sanur')
            ->assertSee('Made Santika')
            ->assertSee('Mangrove')
            ->assertSee('Total entri')
            ->assertSee('Total pohon')
            ->assertSee('Peserta')
            ->assertSee('Jenis tanaman')
            ->assertSee('Distribusi jenis pohon')
            ->assertDontSee('Wayan Lain');
    }

    /**
     * @return array<string, mixed>
     */
    private function recordData(PlantingAccessToken $token, string $name, string $plantType): array
    {
        return [
            'planting_access_token_id' => $token->id,
            'name' => $name,
            'phone' => '081234567890',
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.com',
            'organization' => 'Komunitas Hijau',
            'job_title' => 'Relawan',
            'planted_at' => now()->toDateString(),
            'plant_type' => $plantType,
            'tree_count' => 25,
            'latitude' => '-8.6905000',
            'longitude' => '115.2126000',
            'location_name' => 'Tahura Ngurah Rai',
            'photo_path' => 'planting-documents/example.jpg',
        ];
    }
}
