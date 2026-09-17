@extends('adminlte::page')
@section('title', 'Keamanan Akun')
@push('css')
<style>
    .security-hero { padding: 2rem; border-radius: 14px; color: #fff; background: #172033; }
    .security-hero__icon { display: inline-flex; align-items: center; justify-content: center; width: 54px; height: 54px; margin-bottom: 1rem; border-radius: 14px; color: #172033; background: #8ee0c2; font-size: 1.5rem; }
    .security-hero h1 { margin-bottom: .4rem; font-size: 1.65rem; }
    .security-hero p { max-width: 540px; margin: 0; color: #c5cfdd; }
    .security-card { border: 1px solid #e5eaf1; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
    .security-card__body { max-width: 680px; padding: clamp(1.25rem, 4vw, 2rem); }
    .security-card .form-control { min-height: 48px; border-color: #dce3ed; border-radius: 8px; }
    .security-card .form-control:focus { border-color: #399d7d; box-shadow: 0 0 0 .2rem rgba(57,157,125,.12); }
</style>
@endpush
@section('content_header')<div class="d-flex justify-content-between align-items-center"><div><h3 class="mb-1">Keamanan Akun</h3><span class="text-muted small">Kelola password login Anda</span></div><a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali ke profile</a></div>@stop
@section('content')
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <section class="security-hero mb-4"><div class="security-hero__icon"><i class="bi bi-shield-lock"></i></div><h1>Ganti password secara berkala.</h1><p>Gunakan password yang unik dan sulit ditebak untuk menjaga akun tetap aman.</p></section>
    <div class="security-card"><div class="security-card__body"><form action="{{ route('password.update') }}" method="post">@csrf @method('PUT')<div class="mb-3"><label class="form-label" for="current-password">Password saat ini</label><input id="current-password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>@error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-3"><label class="form-label" for="new-password">Password baru</label><input id="new-password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-4"><label class="form-label" for="password-confirmation">Konfirmasi password baru</label><input id="password-confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required></div><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan password baru</button></form></div></div>
@stop
