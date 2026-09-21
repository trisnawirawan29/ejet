<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_encrypted_service_settings(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'google_maps_api_key' => 'maps-key-test',
            'google_client_id' => 'client-id-test',
            'google_client_secret' => 'client-secret-test',
            'google_redirect_uri' => '/auth/google/callback',
        ])->assertRedirect();

        $setting = AdminSetting::where('key', 'google_maps_api_key')->firstOrFail();
        $this->assertSame('maps-key-test', $setting->value);
        $this->assertNotSame('maps-key-test', $setting->getRawOriginal('value'));
    }

    public function test_admin_can_update_application_name_and_tagline(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'application_name' => 'Ruang Hijau Bali',
            'application_tagline' => 'Menanam hari ini untuk esok yang lebih teduh',
        ])->assertRedirect();

        $this->assertSame('Ruang Hijau Bali', AdminSetting::getValue('application_name'));
        $this->assertSame('Menanam hari ini untuk esok yang lebih teduh', AdminSetting::getValue('application_tagline'));
    }

    public function test_application_branding_is_available_on_login_page(): void
    {
        AdminSetting::create(['key' => 'application_name', 'value' => 'Ruang Hijau Bali']);
        AdminSetting::create(['key' => 'application_tagline', 'value' => 'Tagline pilihan admin']);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Ruang Hijau Bali')
            ->assertSee('Tagline pilihan admin');
    }

    public function test_non_admin_cannot_update_service_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('admin.settings.update'), [
            'google_maps_api_key' => 'maps-key-test',
        ])->assertForbidden();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }
}
