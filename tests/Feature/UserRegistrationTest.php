<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_and_waits_for_admin_verification(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Budi User',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com', 'email_verified_at' => null]);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->unverified()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_verified_user_can_login(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_register_and_login_with_google(): void
    {
        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
        ]);
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'google-token']),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-123', 'name' => 'Google User',
                'email' => 'google@example.com', 'email_verified' => true,
            ]),
        ]);

        $this->withSession(['google_oauth_state' => 'valid-state'])
            ->get(route('google.callback').'?state=valid-state&code=auth-code')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'google@example.com', 'google_id' => 'google-user-123']);
    }

    public function test_non_admin_cannot_access_user_verification_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_verify_user(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $admin->roles()->attach($adminRole);
        $user = User::factory()->unverified()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.verify', $user))
            ->assertRedirect();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
