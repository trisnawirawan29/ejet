<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantingRecordRequest;
use App\Models\Agency;
use App\Models\PlantingAccessToken;
use App\Models\PlantType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPlantingController extends Controller
{
    public function entry(): View
    {
        return view('planting.access');
    }

    public function enter(Request $request): RedirectResponse
    {
        $validated = $request->validate(['token' => ['required', 'string', 'min:8', 'max:120']]);

        return redirect()->route('planting.form', ['token' => $validated['token']]);
    }

    public function create(string $token): View
    {
        $accessToken = $this->resolveAccessToken($token);
        $plantTypes = PlantType::where('is_active', true)->orderBy('name')->pluck('name');
        $agencies = Agency::where('is_active', true)->orderBy('name')->get();

        return view('planting.create', compact('token', 'accessToken', 'plantTypes', 'agencies'));
    }

    public function store(StorePlantingRecordRequest $request, string $token): RedirectResponse
    {
        $accessToken = $this->resolveAccessToken($token);
        $validated = $request->validated();
        $validated['photo_path'] = $request->file('photo')->store('planting-documents', 'public');
        unset($validated['photo'], $validated['consent']);
        $validated['planting_access_token_id'] = $accessToken->id;

        $accessToken->plantingRecords()->create($validated);
        $accessToken->update(['last_used_at' => now()]);

        return redirect()->route('planting.form', ['token' => $token])->with('success', 'Data penanaman berhasil dicatat. Terima kasih atas kontribusi Anda untuk Bali.');
    }

    private function resolveAccessToken(string $token): PlantingAccessToken
    {
        $accessToken = PlantingAccessToken::query()
            ->where('token_hash', hash('sha256', $token))
            ->where('is_active', true)
            ->first();

        abort_if($accessToken === null || ($accessToken->expires_at !== null && $accessToken->expires_at->isPast()), 404);

        return $accessToken;
    }
}
