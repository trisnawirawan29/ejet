<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlantType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlantTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.plant-types.index', ['plantTypes' => PlantType::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.plant-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        PlantType::create($this->validatedData($request));

        return redirect()->route('admin.plant-types.index')->with('success', 'Jenis tanaman berhasil dibuat.');
    }

    public function show(PlantType $plantType): View
    {
        return view('admin.plant-types.edit', compact('plantType'));
    }

    public function edit(PlantType $plantType): View
    {
        return view('admin.plant-types.edit', compact('plantType'));
    }

    public function update(Request $request, PlantType $plantType): RedirectResponse
    {
        $plantType->update($this->validatedData($request, $plantType));

        return redirect()->route('admin.plant-types.index')->with('success', 'Jenis tanaman berhasil diperbarui.');
    }

    public function destroy(PlantType $plantType): RedirectResponse
    {
        $plantType->delete();

        return back()->with('success', 'Jenis tanaman berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?PlantType $plantType = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:120', Rule::unique('plant_types', 'name')->ignore($plantType)], 'description' => ['nullable', 'string', 'max:500'], 'is_active' => ['sometimes', 'boolean']]);
    }
}
