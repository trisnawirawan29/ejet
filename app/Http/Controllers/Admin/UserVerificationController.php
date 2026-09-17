<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserVerificationController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(15),
        ]);
    }

    public function verify(User $user): RedirectResponse
    {
        if ($user->email_verified_at !== null) {
            return back()->with('info', 'Pengguna tersebut sudah diverifikasi.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return back()->with('success', "Pengguna {$user->name} berhasil diverifikasi.");
    }
}
