<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $userStatistics = null;

        if ($user->isAdmin()) {
            $userStatistics = [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'pending' => User::whereNull('email_verified_at')->count(),
                'inactive' => User::where('is_active', false)->count(),
            ];
        }

        $dashboard = [
            'summary' => [
                ['label' => 'Pohon tertanam', 'value' => '12.480', 'detail' => '+18,4%', 'caption' => 'dari target tahunan', 'icon' => 'bi-tree-fill', 'tone' => 'green'],
                ['label' => 'Pegawai terlibat', 'value' => '1.286', 'detail' => '+12,7%', 'caption' => 'pegawai aktif', 'icon' => 'bi-people-fill', 'tone' => 'blue'],
                ['label' => 'Lokasi penanaman', 'value' => '24', 'detail' => '+4', 'caption' => 'lokasi baru tahun ini', 'icon' => 'bi-geo-alt-fill', 'tone' => 'orange'],
                ['label' => 'Survival rate', 'value' => '87,6%', 'detail' => '+3,2%', 'caption' => 'lebih baik dari tahun lalu', 'icon' => 'bi-heart-fill', 'tone' => 'purple'],
            ],
            'monthly' => [
                ['month' => 'Jan', 'value' => 1420], ['month' => 'Feb', 'value' => 980], ['month' => 'Mar', 'value' => 1560],
                ['month' => 'Apr', 'value' => 1080], ['month' => 'Mei', 'value' => 1840], ['month' => 'Jun', 'value' => 1420],
                ['month' => 'Jul', 'value' => 2180], ['month' => 'Agu', 'value' => 2000], ['month' => 'Sep', 'value' => 0],
            ],
            'locations' => [
                ['name' => 'Denpasar Selatan', 'trees' => '3.240', 'rate' => '91%', 'x' => 35, 'y' => 40],
                ['name' => 'Hutan Mangrove Ngurah Rai', 'trees' => '2.180', 'rate' => '88%', 'x' => 57, 'y' => 31],
                ['name' => 'Kawasan Bedugul', 'trees' => '1.860', 'rate' => '84%', 'x' => 72, 'y' => 52],
                ['name' => 'Taman Hutan Raya Ngurah Rai', 'trees' => '2.740', 'rate' => '89%', 'x' => 43, 'y' => 66],
            ],
            'activities' => [
                ['title' => 'Penanaman mangrove di Denpasar', 'meta' => '12 Sep 2026 · 240 pegawai', 'status' => 'Selesai', 'tone' => 'green'],
                ['title' => 'Monitoring kawasan Bedugul', 'meta' => '10 Sep 2026 · 86 pegawai', 'status' => 'Berjalan', 'tone' => 'blue'],
                ['title' => 'Perawatan hutan kota Gianyar', 'meta' => '08 Sep 2026 · 54 pegawai', 'status' => 'Terjadwal', 'tone' => 'orange'],
            ],
            'agencies' => [
                ['name' => 'Dinas Kehutanan dan Lingkungan Hidup', 'short_name' => 'DLHK Provinsi Bali', 'trees' => '4.860', 'people' => '428', 'tone' => 'green'],
                ['name' => 'Dinas Pertanian dan Ketahanan Pangan', 'short_name' => 'Distanpangan Bali', 'trees' => '2.940', 'people' => '286', 'tone' => 'orange'],
                ['name' => 'BPDAS Unda Anyar', 'short_name' => 'BPDAS Unda Anyar', 'trees' => '2.480', 'people' => '194', 'tone' => 'blue'],
                ['name' => 'Perumda dan Desa Adat', 'short_name' => 'Perumda & Desa Adat', 'trees' => '2.200', 'people' => '378', 'tone' => 'purple'],
            ],
            'agenda' => [
                ['date' => '18', 'month' => 'SEP', 'title' => 'Penanaman serentak mangrove', 'location' => 'Tahura Ngurah Rai, Denpasar', 'participants' => '150 pegawai', 'tone' => 'green'],
                ['date' => '24', 'month' => 'SEP', 'title' => 'Gerakan tanam pohon hulu', 'location' => 'Kecamatan Kintamani, Bangli', 'participants' => '90 pegawai', 'tone' => 'orange'],
                ['date' => '02', 'month' => 'OKT', 'title' => 'Monitoring survival rate triwulan', 'location' => 'Seluruh lokasi program Bali', 'participants' => 'Tim monitoring', 'tone' => 'blue'],
            ],
            'target' => ['current' => '12.480', 'goal' => '15.000', 'percentage' => 83],
        ];

        return view('dashboard', compact('userStatistics', 'dashboard'));
    }
}
