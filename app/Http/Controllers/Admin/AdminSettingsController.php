<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminSettingsRequest;
use App\Models\AdminSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(): View
    {
        $keys = ['google_maps_api_key', 'google_client_id', 'google_client_secret', 'google_redirect_uri'];
        $settings = AdminSetting::query()->whereIn('key', $keys)->get()->keyBy('key');

        return view('admin.settings', [
            'settings' => $settings,
            'environmentDefaults' => [
                'google_maps_api_key' => (bool) config('services.google_maps.key'),
                'google_client_id' => (bool) config('services.google.client_id'),
                'google_client_secret' => (bool) config('services.google.client_secret'),
                'google_redirect_uri' => (bool) config('services.google.redirect'),
            ],
        ]);
    }

    public function update(UpdateAdminSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            if ($value === null || $value === '') {
                AdminSetting::query()->where('key', $key)->delete();

                continue;
            }

            AdminSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Pengaturan admin berhasil disimpan.');
    }
}
