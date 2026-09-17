<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $statistics = null;

        if ($user->isAdmin()) {
            $statistics = [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'pending' => User::whereNull('email_verified_at')->count(),
                'inactive' => User::where('is_active', false)->count(),
            ];
        }

        return view('dashboard', compact('statistics'));
    }
}
