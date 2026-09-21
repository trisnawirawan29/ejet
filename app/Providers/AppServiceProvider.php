<?php

namespace App\Providers;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::define('access-admin', fn ($user) => $user->isAdmin());

        View::composer('*', function ($view): void {
            $request = app()->bound('request') ? request() : null;
            $branding = $request?->attributes->get('application.branding');

            if (! is_array($branding)) {
                $branding = AdminSetting::branding();
                $request?->attributes->set('application.branding', $branding);
            }

            $view->with($branding);
        });
    }
}
