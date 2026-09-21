<?php

namespace App\Http\Controllers;

use App\Models\PlantingAccessToken;
use App\Models\PlantingRecord;
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

        $records = PlantingRecord::query()
            ->latest('planted_at')
            ->get(['name', 'email', 'organization', 'planted_at', 'plant_type', 'tree_count', 'latitude', 'longitude', 'location_name', 'created_at']);
        $activeTokens = PlantingAccessToken::query()->where('is_active', true)->count();
        $participantCount = $records->map(fn (PlantingRecord $record): string => $record->email ?: $record->name)->unique()->count();
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyRecords = $records->filter(fn (PlantingRecord $record): bool => $record->planted_at->year === now()->year)->groupBy(fn (PlantingRecord $record): int => $record->planted_at->month);
        $monthly = collect(range(1, 12))->map(fn (int $month): array => [
            'month' => $monthNames[$month - 1],
            'value' => $monthlyRecords->get($month, collect())->sum('tree_count'),
        ])->all();
        $locations = $records->filter(fn (PlantingRecord $record): bool => filled($record->location_name))->groupBy('location_name')->map(function ($locationRecords, string $name): array {
            $firstRecord = $locationRecords->first();

            return [
                'name' => $name,
                'trees' => $locationRecords->sum('tree_count'),
                'records' => $locationRecords->count(),
                'latitude' => (float) $firstRecord->latitude,
                'longitude' => (float) $firstRecord->longitude,
            ];
        })->sortByDesc('trees')->values()->all();
        $agencies = $records->filter(fn (PlantingRecord $record): bool => filled($record->organization))->groupBy('organization')->map(function ($agencyRecords, string $name): array {
            return [
                'name' => $name,
                'short_name' => $name,
                'trees' => $agencyRecords->sum('tree_count'),
                'people' => $agencyRecords->map(fn (PlantingRecord $record): string => $record->email ?: $record->name)->unique()->count(),
                'tone' => 'green',
            ];
        })->sortByDesc('trees')->values()->all();
        $activities = $records->take(5)->map(fn (PlantingRecord $record): array => [
            'title' => "Penanaman {$record->plant_type} oleh {$record->name}",
            'meta' => $record->planted_at->format('d M Y').' · '.$record->tree_count.' pohon',
            'status' => 'Tercatat',
            'tone' => 'green',
        ])->all();
        $totalTrees = $records->sum('tree_count');

        $dashboard = [
            'summary' => [
                ['label' => 'Pohon tertanam', 'value' => number_format($totalTrees, 0, ',', '.'), 'detail' => '', 'caption' => 'dari seluruh data masuk', 'icon' => 'bi-tree-fill', 'tone' => 'green'],
                ['label' => 'Peserta', 'value' => number_format($participantCount, 0, ',', '.'), 'detail' => '', 'caption' => 'peserta tercatat', 'icon' => 'bi-people-fill', 'tone' => 'blue'],
                ['label' => 'Lokasi penanaman', 'value' => number_format(count($locations), 0, ',', '.'), 'detail' => '', 'caption' => 'lokasi dari data masuk', 'icon' => 'bi-geo-alt-fill', 'tone' => 'orange'],
                ['label' => 'Token aktif', 'value' => number_format($activeTokens, 0, ',', '.'), 'detail' => '', 'caption' => 'akses publik aktif', 'icon' => 'bi-qr-code', 'tone' => 'purple'],
            ],
            'monthly' => $monthly,
            'locations' => $locations,
            'activities' => $activities,
            'agencies' => $agencies,
            'agenda' => [],
            'target' => null,
            'totalTrees' => $totalTrees,
            'year' => now()->year,
        ];

        return view('dashboard', compact('userStatistics', 'dashboard'));
    }
}
