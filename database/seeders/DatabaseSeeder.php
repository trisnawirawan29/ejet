<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\Agency;
use App\Models\PlantingAccessToken;
use App\Models\PlantType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => 'password']
        );
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->roles()->syncWithoutDetaching([$admin->id]);

        AdminSetting::firstOrCreate(
            ['key' => 'application_name'],
            ['value' => AdminSetting::DEFAULT_APPLICATION_NAME],
        );
        AdminSetting::firstOrCreate(
            ['key' => 'application_tagline'],
            ['value' => AdminSetting::DEFAULT_APPLICATION_TAGLINE],
        );

        $plantingToken = config('planting.access_token');
        PlantingAccessToken::updateOrCreate(
            ['token_hash' => hash('sha256', $plantingToken)],
            ['label' => 'Akses penanaman Provinsi Bali', 'token_secret' => $plantingToken, 'is_active' => true],
        );

        foreach (['Mahoni', 'Trembesi', 'Mangrove', 'Beringin', 'Bambu', 'Nyamplung'] as $name) {
            PlantType::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        foreach ([
            ['name' => 'Dinas Kehutanan dan Lingkungan Hidup Provinsi Bali', 'short_name' => 'DLHK Provinsi Bali'],
            ['name' => 'Dinas Pertanian dan Ketahanan Pangan Provinsi Bali', 'short_name' => 'Distanpangan Bali'],
            ['name' => 'Balai Pengelolaan DAS Unda Anyar', 'short_name' => 'BPDAS Unda Anyar'],
            ['name' => 'Perumda dan Desa Adat Bali', 'short_name' => 'Perumda & Desa Adat'],
        ] as $agency) {
            Agency::firstOrCreate(['name' => $agency['name']], [...$agency, 'is_active' => true]);
        }

        $this->call(DemoDataSeeder::class);
    }
}
