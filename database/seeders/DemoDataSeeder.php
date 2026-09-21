<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\PlantingAccessToken;
use App\Models\PlantingRecord;
use App\Models\PlantType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $roles = $this->seedRoles();
        $this->seedUsers($roles);

        $plantTypes = PlantType::query()->pluck('name', 'name');
        $agencies = Agency::query()->pluck('short_name', 'short_name');
        $tokens = $this->seedActivities();

        $this->seedPlantingRecords($tokens, $plantTypes, $agencies);
    }

    /**
     * @return array<string, Role>
     */
    private function seedRoles(): array
    {
        $roles = [];

        foreach ([
            'operator' => 'Operator Kegiatan',
            'verifikator' => 'Verifikator Data',
        ] as $slug => $name) {
            $roles[$slug] = Role::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        return $roles;
    }

    /**
     * @param  array<string, Role>  $roles
     */
    private function seedUsers(array $roles): void
    {
        $users = [
            [
                'email' => 'operator@example.com',
                'name' => 'Operator Kegiatan',
                'phone' => '081234567890',
                'job_title' => 'Koordinator Lapangan',
                'company' => 'DLHK Provinsi Bali',
                'role' => 'operator',
            ],
            [
                'email' => 'verifikator@example.com',
                'name' => 'Verifikator Data',
                'phone' => '081234567891',
                'job_title' => 'Staf Pengelolaan Data',
                'company' => 'BPDAS Unda Anyar',
                'role' => 'verifikator',
            ],
            [
                'email' => 'relawan@example.com',
                'name' => 'Relawan Penanaman',
                'phone' => '081234567892',
                'job_title' => 'Relawan',
                'company' => 'Komunitas Hijau Bali',
                'role' => null,
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [...$userData, 'password' => 'password', 'is_active' => true],
            );

            $user->forceFill(['email_verified_at' => now()])->save();

            if ($role !== null) {
                $user->roles()->syncWithoutDetaching([$roles[$role]->id]);
            }
        }
    }

    /**
     * @return array<string, PlantingAccessToken>
     */
    private function seedActivities(): array
    {
        $activities = [
            'bali-utama' => [
                'token' => (string) config('planting.access_token'),
                'label' => 'Penanaman Serentak Provinsi Bali',
                'event_date' => '2026-09-20',
                'location_name' => 'Tahura Ngurah Rai, Denpasar',
                'latitude' => -8.7181,
                'longitude' => 115.1958,
            ],
            'mangrove' => [
                'token' => 'BALI-MANGROVE-2026',
                'label' => 'Aksi Rehabilitasi Mangrove',
                'event_date' => '2026-08-17',
                'location_name' => 'Pesisir Serangan, Denpasar',
                'latitude' => -8.7335,
                'longitude' => 115.2386,
            ],
            'hutan-desa' => [
                'token' => 'BALI-HUTAN-DESA-2026',
                'label' => 'Gerakan Hutan Desa Berkelanjutan',
                'event_date' => '2026-07-12',
                'location_name' => 'Desa Pelaga, Badung',
                'latitude' => -8.2808,
                'longitude' => 115.2164,
            ],
        ];

        $tokens = [];

        foreach ($activities as $key => $activity) {
            $plainToken = $activity['token'];
            unset($activity['token']);

            $tokens[$key] = PlantingAccessToken::updateOrCreate(
                ['token_hash' => hash('sha256', $plainToken)],
                [...$activity, 'token_secret' => $plainToken, 'is_active' => true],
            );
        }

        return $tokens;
    }

    /**
     * @param  array<string, PlantingAccessToken>  $tokens
     * @param  Collection<string, string>  $plantTypes
     * @param  Collection<string, string>  $agencies
     */
    private function seedPlantingRecords(array $tokens, Collection $plantTypes, Collection $agencies): void
    {
        $records = [
            ['activity' => 'bali-utama', 'name' => 'I Putu Adi Saputra', 'phone' => '081300000001', 'email' => 'adi@example.com', 'organization' => 'DLHK Provinsi Bali', 'job_title' => 'Pegawai Negeri Sipil', 'planted_at' => '2026-09-20', 'plant_type' => 'Mahoni', 'tree_count' => 12, 'latitude' => -8.7180, 'longitude' => 115.1956],
            ['activity' => 'bali-utama', 'name' => 'Ni Luh Sari Dewi', 'phone' => '081300000002', 'email' => 'sari@example.com', 'organization' => 'Distanpangan Bali', 'job_title' => 'Penyuluh Pertanian', 'planted_at' => '2026-09-20', 'plant_type' => 'Beringin', 'tree_count' => 8, 'latitude' => -8.7183, 'longitude' => 115.1960],
            ['activity' => 'bali-utama', 'name' => 'Made Yoga Pratama', 'phone' => '081300000003', 'email' => 'yoga@example.com', 'organization' => 'Perumda & Desa Adat', 'job_title' => 'Relawan', 'planted_at' => '2026-09-20', 'plant_type' => 'Trembesi', 'tree_count' => 15, 'latitude' => -8.7182, 'longitude' => 115.1959],
            ['activity' => 'mangrove', 'name' => 'Komang Wira Negara', 'phone' => '081300000004', 'email' => 'wira@example.com', 'organization' => 'BPDAS Unda Anyar', 'job_title' => 'Petugas Lapangan', 'planted_at' => '2026-08-17', 'plant_type' => 'Mangrove', 'tree_count' => 25, 'latitude' => -8.7334, 'longitude' => 115.2385],
            ['activity' => 'mangrove', 'name' => 'Ayu Prameswari', 'phone' => '081300000005', 'email' => 'ayu@example.com', 'organization' => 'DLHK Provinsi Bali', 'job_title' => 'Mahasiswa', 'planted_at' => '2026-08-17', 'plant_type' => 'Mangrove', 'tree_count' => 18, 'latitude' => -8.7336, 'longitude' => 115.2387],
            ['activity' => 'hutan-desa', 'name' => 'Ketut Suardana', 'phone' => '081300000006', 'email' => 'ketut@example.com', 'organization' => 'Perumda & Desa Adat', 'job_title' => 'Kepala Dusun', 'planted_at' => '2026-07-12', 'plant_type' => 'Nyamplung', 'tree_count' => 20, 'latitude' => -8.2807, 'longitude' => 115.2163],
            ['activity' => 'hutan-desa', 'name' => 'Desak Made Utami', 'phone' => '081300000007', 'email' => 'utami@example.com', 'organization' => 'Distanpangan Bali', 'job_title' => 'Kelompok Tani', 'planted_at' => '2026-07-12', 'plant_type' => 'Bambu', 'tree_count' => 30, 'latitude' => -8.2809, 'longitude' => 115.2165],
        ];

        foreach ($records as $record) {
            $activity = $record['activity'];
            unset($record['activity']);

            PlantingRecord::updateOrCreate(
                [
                    'planting_access_token_id' => $tokens[$activity]->id,
                    'email' => $record['email'],
                    'planted_at' => $record['planted_at'],
                ],
                [
                    ...$record,
                    'plant_type' => $plantTypes[$record['plant_type']] ?? $record['plant_type'],
                    'organization' => $agencies[$record['organization']] ?? $record['organization'],
                    'location_name' => $tokens[$activity]->location_name,
                    'photo_path' => 'seeded/planting-placeholder.jpg',
                ],
            );
        }
    }
}
