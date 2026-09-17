@extends('adminlte::page')

@section('title', 'Pengaturan Admin')

@push('css')
    <style>
        .settings-card { border: 1px solid #e5eee8; border-radius: 16px; box-shadow: 0 10px 28px rgba(23, 70, 50, .06); }
        .settings-card .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #edf2ee; background: #fbfdfb; }
        .settings-card .card-body { padding: 1.5rem; }
        .settings-card .form-label { color: #365348; font-size: .82rem; font-weight: 700; }
        .settings-card .form-control { min-height: 44px; border-color: #dce8df; border-radius: 10px; }
        .settings-card .form-control:focus { border-color: #6db895; box-shadow: 0 0 0 .2rem rgba(45, 155, 112, .12); }
        .settings-note { color: #718096; font-size: .8rem; }
        .settings-status { color: #198754; font-size: .75rem; font-weight: 600; }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div><h3 class="mb-1">Pengaturan Admin</h3><span class="text-muted small">Kelola integrasi layanan aplikasi</span></div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
    @endif

    <div class="alert alert-info border-0"><i class="bi bi-shield-lock me-2"></i>API key dan secret disimpan terenkripsi di database. Kosongkan field untuk menghapus nilai dari database dan kembali menggunakan fallback `.env`.</div>

    <div class="card settings-card">
        <div class="card-header"><h5 class="mb-1"><i class="bi bi-plug text-success me-2"></i>Integrasi Google</h5><p class="settings-note mb-0">Konfigurasi Google Maps dan Login dengan Google.</p></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label" for="google-maps-api-key">Google Maps API key</label>
                        <input id="google-maps-api-key" type="password" name="google_maps_api_key" value="{{ old('google_maps_api_key') }}" class="form-control @error('google_maps_api_key') is-invalid @enderror" placeholder="{{ $settings->has('google_maps_api_key') || $environmentDefaults['google_maps_api_key'] ? 'Tersimpan, isi hanya jika ingin mengganti' : 'Masukkan Google Maps API key' }}" autocomplete="new-password">
                        @if ($settings->has('google_maps_api_key') || $environmentDefaults['google_maps_api_key'])<div class="settings-status mt-2"><i class="bi bi-check-circle me-1"></i>Konfigurasi tersedia</div>@endif
                        @error('google_maps_api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="google-client-id">Google OAuth Client ID</label>
                        <input id="google-client-id" type="password" name="google_client_id" value="{{ old('google_client_id') }}" class="form-control @error('google_client_id') is-invalid @enderror" placeholder="{{ $settings->has('google_client_id') || $environmentDefaults['google_client_id'] ? 'Tersimpan, isi hanya jika ingin mengganti' : 'Masukkan Client ID' }}" autocomplete="new-password">
                        @if ($settings->has('google_client_id') || $environmentDefaults['google_client_id'])<div class="settings-status mt-2"><i class="bi bi-check-circle me-1"></i>Konfigurasi tersedia</div>@endif
                        @error('google_client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="google-client-secret">Google OAuth Client Secret</label>
                        <input id="google-client-secret" type="password" name="google_client_secret" value="{{ old('google_client_secret') }}" class="form-control @error('google_client_secret') is-invalid @enderror" placeholder="{{ $settings->has('google_client_secret') || $environmentDefaults['google_client_secret'] ? 'Tersimpan, isi hanya jika ingin mengganti' : 'Masukkan Client Secret' }}" autocomplete="new-password">
                        @if ($settings->has('google_client_secret') || $environmentDefaults['google_client_secret'])<div class="settings-status mt-2"><i class="bi bi-check-circle me-1"></i>Konfigurasi tersedia</div>@endif
                        @error('google_client_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="google-redirect-uri">Google OAuth Redirect URI</label>
                        <input id="google-redirect-uri" name="google_redirect_uri" value="{{ old('google_redirect_uri', $settings->get('google_redirect_uri')?->value ?? '') }}" class="form-control @error('google_redirect_uri') is-invalid @enderror" placeholder="/auth/google/callback">
                        <div class="settings-note mt-2">Gunakan URI yang sama pada Google Cloud Console. Default aplikasi: <code>/auth/google/callback</code>.</div>
                        @error('google_redirect_uri')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4"><button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Simpan pengaturan</button></div>
            </form>
        </div>
    </div>
@stop
