<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\PlantingAccessToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlantingAccessTokenController extends Controller
{
    public function index(): View
    {
        return view('admin.planting-tokens.index', [
            'tokens' => PlantingAccessToken::withCount('plantingRecords')->latest()->get(),
            'googleMapsApiKey' => AdminSetting::getValue('google_maps_api_key', config('services.google_maps.key')),
        ]);
    }

    public function create(): View
    {
        return view('admin.planting-tokens.create', ['googleMapsApiKey' => AdminSetting::getValue('google_maps_api_key', config('services.google_maps.key'))]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $plainToken = Str::upper(Str::random(12));
        $accessToken = PlantingAccessToken::create([...$validated, 'token_hash' => hash('sha256', $plainToken), 'token_secret' => $plainToken]);

        return redirect()->route('admin.planting-tokens.index')->with('created_token', ['value' => $plainToken, 'url' => route('planting.form', ['token' => $plainToken]), 'id' => $accessToken->id]);
    }

    public function show(PlantingAccessToken $plantingToken): View
    {
        return view('admin.planting-tokens.show', ['token' => $plantingToken->loadCount('plantingRecords')]);
    }

    public function edit(PlantingAccessToken $plantingToken): View
    {
        return view('admin.planting-tokens.edit', ['token' => $plantingToken, 'googleMapsApiKey' => AdminSetting::getValue('google_maps_api_key', config('services.google_maps.key'))]);
    }

    public function update(Request $request, PlantingAccessToken $plantingToken): RedirectResponse
    {
        $plantingToken->update($this->validatedData($request));

        return redirect()->route('admin.planting-tokens.index')->with('success', 'Token akses berhasil diperbarui.');
    }

    public function destroy(PlantingAccessToken $plantingToken): RedirectResponse
    {
        if ($plantingToken->plantingRecords()->exists()) {
            return back()->with('error', 'Token yang sudah memiliki data penanaman tidak dapat dihapus. Nonaktifkan token tersebut.');
        }

        $plantingToken->delete();

        return back()->with('success', 'Token akses berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'location_name' => ['required', 'string', 'max:180'],
            'latitude' => ['required', 'numeric', 'between:-9.2,-8.0'],
            'longitude' => ['required', 'numeric', 'between:114.0,116.0'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
