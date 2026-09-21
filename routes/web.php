<?php

use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\PlantingAccessTokenController;
use App\Http\Controllers\Admin\PlantTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPlantingController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::get('/tanam', [PublicPlantingController::class, 'entry'])->name('planting.entry');
Route::post('/tanam', [PublicPlantingController::class, 'enter'])->name('planting.enter');
Route::get('/tanam/{token}', [PublicPlantingController::class, 'create'])->name('planting.form');
Route::get('/tanam/{token}/terima-kasih', [PublicPlantingController::class, 'thanks'])->name('planting.thanks');
Route::post('/tanam/{token}', [PublicPlantingController::class, 'store'])->middleware('throttle:10,1')->name('planting.store');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.destroy');
    Route::get('/profile/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/profile/password', [PasswordController::class, 'update'])->name('password.update');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('planting-tokens', PlantingAccessTokenController::class)->parameters(['planting-tokens' => 'plantingToken']);
        Route::get('/planting-tokens/{plantingToken}/records', [PlantingAccessTokenController::class, 'records'])->name('planting-tokens.records');
        Route::resource('plant-types', PlantTypeController::class)->parameters(['plant-types' => 'plantType']);
        Route::resource('agencies', AgencyController::class);
        Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});
