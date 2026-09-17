@extends('adminlte::page')
@php
    use Illuminate\Support\Facades\Storage;
    $profilePhotoUrl = $user->profile_photo_path ? Storage::disk('public')->url($user->profile_photo_path) : null;
@endphp
@section('title', 'Profil Saya')
@push('css')
<style>
    .profile-hero { position: relative; overflow: hidden; min-height: 235px; padding: 2rem; border-radius: 14px; color: #fff; background: #172033; box-shadow: 0 12px 28px rgba(15,23,42,.1); }
    .profile-hero__eyebrow { position: relative; z-index: 1; margin-bottom: 1.7rem; color: #8ee0c2; font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
    .profile-hero__content { position: relative; z-index: 1; display: flex; align-items: center; gap: 1.25rem; }
    .profile-avatar { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 86px; height: 86px; border: 4px solid rgba(255,255,255,.22); border-radius: 50%; color: #172033; background: #8ee0c2; font-size: 1.9rem; font-weight: 700; object-fit: cover; }
    .profile-hero h1 { margin: 0 0 .35rem; font-size: clamp(1.5rem, 3vw, 2.1rem); }
    .profile-hero p { margin: 0; color: #c5cfdd; }
    .profile-meta { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
    .profile-meta span { padding: .35rem .65rem; border: 1px solid rgba(255,255,255,.14); border-radius: 999px; color: #dbe4ef; font-size: .76rem; }
    .profile-form-card { border: 1px solid #e5eaf1; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
    .profile-form-card__header { padding: 1.3rem 1.5rem; border-bottom: 1px solid #edf0f5; }
    .profile-form-card__header h2 { margin: 0 0 .25rem; color: #172033; font-size: 1.15rem; }
    .profile-form-card__header p { margin: 0; color: #718096; font-size: .86rem; }
    .profile-form-card__body { padding: 1.5rem; }
    .profile-section { padding-top: 1.4rem; margin-top: 1.4rem; border-top: 1px solid #edf0f5; }
    .profile-section:first-child { padding-top: 0; margin-top: 0; border-top: 0; }
    .profile-section__title { display: flex; align-items: center; gap: .5rem; margin-bottom: 1rem; color: #172033; font-size: .95rem; font-weight: 700; }
    .profile-section__title i { color: #238565; }
    .profile-form .form-label { color: #374151; font-size: .86rem; font-weight: 600; }
    .profile-form .form-control { min-height: 46px; border-color: #dce3ed; border-radius: 8px; }
    .profile-form .form-control:focus { border-color: #399d7d; box-shadow: 0 0 0 .2rem rgba(57,157,125,.12); }
    .profile-form textarea.form-control { min-height: 105px; }
    .profile-photo-box { display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; }
    .profile-photo-box__preview { width: 58px; height: 58px; border-radius: 50%; color: #172033; background: #d7f3e9; font-size: 1.3rem; object-fit: cover; }
    .profile-photo-box__text { flex: 1; min-width: 0; }
    .profile-photo-box__text strong { display: block; color: #172033; font-size: .9rem; }
    .profile-photo-box__text span { color: #718096; font-size: .78rem; }
    @media (max-width: 576px) { .profile-hero { min-height: 205px; padding: 1.5rem; } .profile-hero__eyebrow { margin-bottom: 1.2rem; } .profile-avatar { width: 70px; height: 70px; } .profile-hero__content { align-items: flex-start; } .profile-form-card__body { padding: 1.15rem; } .profile-photo-box { align-items: flex-start; flex-wrap: wrap; } }
</style>
@endpush
@section('content_header')<div class="d-flex justify-content-between align-items-center"><div><h3 class="mb-1">Profil Saya</h3><span class="text-muted small">Kelola informasi akun Anda</span></div><div class="d-flex gap-2"><a href="{{ route('password.edit') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-shield-lock me-1"></i>Ganti password</a><a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Dashboard</a></div></div>@stop
@section('content')
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <div class="profile-hero mb-4"><div class="profile-hero__eyebrow">Account settings</div><div class="profile-hero__content">@if ($profilePhotoUrl)<img src="{{ $profilePhotoUrl }}" class="profile-avatar" alt="Foto {{ $user->name }}">@else<div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>@endif<div><h1>{{ $user->name }}</h1><p>{{ $user->email }}</p><div class="profile-meta"><span><i class="bi bi-shield-check me-1"></i>{{ $user->roles->pluck('name')->join(', ') ?: 'Pengguna' }}</span><span><i class="bi bi-{{ $user->is_active ? 'check-circle' : 'slash-circle' }} me-1"></i>{{ $user->is_active ? 'Akun aktif' : 'Akun nonaktif' }}</span></div></div></div></div>
    <div class="profile-form-card"><div class="profile-form-card__header"><h2>Informasi profile</h2><p>Lengkapi data agar profile Anda lebih informatif.</p></div><div class="profile-form-card__body"><form class="profile-form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">@csrf @method('PUT')
        <div class="profile-section"><div class="profile-section__title"><i class="bi bi-camera"></i>Foto profile</div><div class="profile-photo-box"><div class="profile-photo-box__preview d-flex align-items-center justify-content-center">@if ($profilePhotoUrl)<img src="{{ $profilePhotoUrl }}" class="profile-photo-box__preview" alt="Foto profile">@else<i class="bi bi-person"></i>@endif</div><div class="profile-photo-box__text"><strong>Pilih foto terbaik Anda</strong><span>JPG, PNG, atau WEBP, maksimal 2 MB.</span></div><label for="profile-photo" class="btn btn-sm btn-outline-primary mb-0"><i class="bi bi-upload me-1"></i>Pilih foto</label><input id="profile-photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="d-none">@error('profile_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>@if ($profilePhotoUrl)<div class="mt-2"><button type="submit" form="remove-profile-photo" class="btn btn-link btn-sm text-danger px-0"><i class="bi bi-trash me-1"></i>Hapus foto</button></div>@endif</div>
        <div class="profile-section"><div class="profile-section__title"><i class="bi bi-person-vcard"></i>Informasi dasar</div><div class="row g-3"><div class="col-md-6"><label class="form-label" for="profile-name">Nama lengkap <span class="text-danger">*</span></label><input id="profile-name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label class="form-label" for="profile-email">Email <span class="text-danger">*</span></label><input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div></div>
        <div class="profile-section"><div class="profile-section__title"><i class="bi bi-briefcase"></i>Kontak dan pekerjaan <span class="text-muted small fw-normal">(opsional)</span></div><div class="row g-3"><div class="col-md-6"><label class="form-label" for="profile-phone">Nomor telepon</label><input id="profile-phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror" placeholder="08123456789">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label class="form-label" for="profile-job-title">Jabatan</label><input id="profile-job-title" name="job_title" value="{{ old('job_title', $user->job_title) }}" class="form-control @error('job_title') is-invalid @enderror" placeholder="Product Manager">@error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label class="form-label" for="profile-company">Perusahaan</label><input id="profile-company" name="company" value="{{ old('company', $user->company) }}" class="form-control @error('company') is-invalid @enderror" placeholder="Nama perusahaan">@error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label class="form-label" for="profile-birth-date">Tanggal lahir</label><input id="profile-birth-date" type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="form-control @error('date_of_birth') is-invalid @enderror">@error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-12"><label class="form-label" for="profile-address">Alamat</label><textarea id="profile-address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat tempat tinggal">{{ old('address', $user->address) }}</textarea>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div></div>
        <div class="profile-section"><div class="profile-section__title"><i class="bi bi-chat-left-text"></i>Tentang saya <span class="text-muted small fw-normal">(opsional)</span></div><label class="form-label" for="profile-bio">Bio singkat</label><textarea id="profile-bio" name="bio" class="form-control @error('bio') is-invalid @enderror" placeholder="Ceritakan sedikit tentang diri Anda">{{ old('bio', $user->bio) }}</textarea>@error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="d-flex justify-content-end mt-4"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan perubahan</button></div>
    </form></div></div>
    @if ($profilePhotoUrl)<form id="remove-profile-photo" action="{{ route('profile.photo.destroy') }}" method="post" class="d-none">@csrf @method('DELETE')</form>@endif
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('profile-photo');
    const preview = document.querySelector('.profile-photo-box__preview');
    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/') || file.size > 2 * 1024 * 1024) {
            input.value = '';
            return;
        }

        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = 'Preview foto profile';
        image.className = 'profile-photo-box__preview';
        preview.replaceWith(image);
    });
});
</script>
@endpush
