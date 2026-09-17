<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->orderByDesc('created_at')->orderByDesc('id')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedUserData($request);
        $validated['is_active'] = $request->boolean('is_active');
        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        $user = User::create([...$validated, 'email_verified_at' => now()]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validatedUserData($request, $user);
        $validated['is_active'] = $request->boolean('is_active');
        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        if (($validated['password'] ?? null) === null || $validated['password'] === '') {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function verify(User $user): RedirectResponse
    {
        if ($user->email_verified_at !== null) {
            return back()->with('info', 'Pengguna tersebut sudah diverifikasi.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return back()->with('success', "Pengguna {$user->name} berhasil diverifikasi.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return back()->with('error', 'Status akun yang sedang digunakan tidak dapat diubah.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status pengguna berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedUserData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ]);
    }
}
