<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(): View
    {
        return view('admin.agencies.index', ['agencies' => Agency::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.agencies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Agency::create($this->validatedData($request));

        return redirect()->route('admin.agencies.index')->with('success', 'Instansi berhasil dibuat.');
    }

    public function show(Agency $agency): View
    {
        return view('admin.agencies.edit', compact('agency'));
    }

    public function edit(Agency $agency): View
    {
        return view('admin.agencies.edit', compact('agency'));
    }

    public function update(Request $request, Agency $agency): RedirectResponse
    {
        $agency->update($this->validatedData($request, $agency));

        return redirect()->route('admin.agencies.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Agency $agency): RedirectResponse
    {
        $agency->delete();

        return back()->with('success', 'Instansi berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Agency $agency = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:160', Rule::unique('agencies', 'name')->ignore($agency)], 'short_name' => ['nullable', 'string', 'max:80'], 'description' => ['nullable', 'string', 'max:500'], 'is_active' => ['sometimes', 'boolean']]);
    }
}
