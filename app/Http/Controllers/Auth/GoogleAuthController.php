<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->googleSetting('client_id') || ! $this->googleSetting('client_secret')) {
            return redirect()->route('login')->with('error', 'Login Google belum dikonfigurasi oleh administrator.');
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $this->googleSetting('client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->pull('google_oauth_state');
        if (! $state || ! $request->filled('state') || ! hash_equals($state, $request->string('state')->toString())) {
            return redirect()->route('login')->with('error', 'Sesi login Google tidak valid. Silakan coba lagi.');
        }

        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()->route('login')->with('error', 'Login Google dibatalkan.');
        }

        try {
            $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->string('code')->toString(),
                'client_id' => $this->googleSetting('client_id'),
                'client_secret' => $this->googleSetting('client_secret'),
                'redirect_uri' => $this->redirectUri(),
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $googleUser = Http::withToken($token['access_token'])
                ->get('https://openidconnect.googleapis.com/v1/userinfo')
                ->throw()
                ->json();
        } catch (Throwable) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        if (empty($googleUser['sub']) || empty($googleUser['email']) || ! filter_var($googleUser['email_verified'] ?? false, FILTER_VALIDATE_BOOL)) {
            return redirect()->route('login')->with('error', 'Akun Google tidak dapat diverifikasi.');
        }

        $user = User::where('google_id', $googleUser['sub'])->first()
            ?? User::where('email', $googleUser['email'])->first();

        if ($user && ! $user->is_active) {
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan.');
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser['name'] ?? Str::before($googleUser['email'], '@'),
                'email' => $googleUser['email'],
                'password' => Str::random(40),
                'is_active' => true,
            ]);
        }

        $user->forceFill([
            'google_id' => $googleUser['sub'],
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Berhasil masuk dengan Google.');
    }

    private function redirectUri(): string
    {
        return url($this->googleSetting('redirect', '/auth/google/callback'));
    }

    private function googleSetting(string $key, mixed $default = null): mixed
    {
        return AdminSetting::getValue('google_'.$key, $default ?? config('services.google.'.$key));
    }
}
