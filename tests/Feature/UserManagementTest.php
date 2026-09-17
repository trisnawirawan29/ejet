<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_with_role(): void
    {
        $admin = $this->admin();
        $editor = Role::create(['name' => 'Editor', 'slug' => 'editor']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New User', 'email' => 'new@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'roles' => [$editor->id], 'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('editor'));
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_admin_can_update_role_and_status(): void
    {
        $admin = $this->admin();
        $editor = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $user = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'roles' => [$editor->id],
        ])->assertRedirect(route('admin.users.index'));

        $this->assertFalse($user->fresh()->is_active);
        $this->assertTrue($user->fresh()->hasRole('editor'));
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_user_can_update_own_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name', 'email' => 'updated@example.com',
            'phone' => '08123456789', 'job_title' => 'Product Manager',
            'company' => 'Ejet', 'date_of_birth' => '1990-05-12',
            'address' => 'Jl. Merdeka No. 1', 'bio' => 'Suka membangun produk.',
        ])->assertRedirect();

        $updatedUser = $user->fresh();
        $this->assertSame('Updated Name', $updatedUser->name);
        $this->assertSame('08123456789', $updatedUser->phone);
        $this->assertSame('Product Manager', $updatedUser->job_title);
        $this->assertSame('1990-05-12', $updatedUser->date_of_birth->format('Y-m-d'));
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_can_upload_and_remove_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name, 'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg'),
        ])->assertRedirect();

        $photoPath = $user->fresh()->profile_photo_path;
        Storage::disk('public')->assertExists($photoPath);

        $this->actingAs($user)->delete(route('profile.photo.destroy'))->assertRedirect();

        Storage::disk('public')->assertMissing($photoPath);
        $this->assertNull($user->fresh()->profile_photo_path);
    }

    public function test_admin_dashboard_shows_user_statistics(): void
    {
        $admin = $this->admin();
        User::factory()->create();
        User::factory()->unverified()->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee('Pengguna terverifikasi')
            ->assertSee('Menunggu verifikasi')
            ->assertSee('Pengguna nonaktif');
    }

    public function test_non_admin_dashboard_does_not_show_user_statistics(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertDontSee('Pengguna terverifikasi')
            ->assertDontSee('Menunggu verifikasi');
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $admin->roles()->attach($role);

        return $admin;
    }
}
