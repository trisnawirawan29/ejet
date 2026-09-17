@extends('adminlte::page')

@section('title', 'Dashboard')
@push('css')
<style>
    .dashboard-welcome { position: relative; overflow: hidden; padding: 1.75rem 2rem; color: #fff; border-radius: 12px; background: #172033; }
    .dashboard-welcome::after { position: absolute; right: 2rem; bottom: -3.5rem; width: 150px; height: 150px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; content: ''; }
    .dashboard-welcome__eyebrow { margin-bottom: .5rem; color: #8ee0c2; font-size: .75rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .dashboard-welcome h1 { position: relative; z-index: 1; margin: 0 0 .35rem; font-size: clamp(1.45rem, 3vw, 2.15rem); }
    .dashboard-welcome p { position: relative; z-index: 1; max-width: 640px; margin: 0; color: #c5cfdd; }
    .dashboard-stat { height: 100%; border: 1px solid #e5eaf1; border-radius: 10px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
    .dashboard-stat__icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 10px; font-size: 1.25rem; }
    .dashboard-stat__number { margin: .75rem 0 .1rem; color: #172033; font-size: 1.8rem; font-weight: 700; line-height: 1; }
    .dashboard-stat__label { color: #718096; font-size: .85rem; }
    .dashboard-section-title { color: #172033; font-size: 1.05rem; font-weight: 700; }
    .dashboard-status { display: flex; align-items: center; gap: .75rem; padding: 1rem; border: 1px solid #e5eaf1; border-radius: 10px; background: #fff; }
    .dashboard-status__icon { color: #238565; font-size: 1.35rem; }
</style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center"><h3 class="mb-0">Dashboard</h3><span class="badge text-bg-primary">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Pengguna' }}</span></div>
@stop

@section('content')
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="dashboard-welcome mb-4">
        <div class="dashboard-welcome__eyebrow">Ejet workspace</div>
        <h1>Halo, {{ auth()->user()->name }}.</h1>
        <p>Semua yang Anda perlukan untuk memantau aktivitas workspace ada di sini.</p>
    </section>

    @if ($statistics)
        <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="dashboard-section-title mb-0">Ringkasan pengguna</h2><a href="{{ route('admin.users.index') }}" class="small text-decoration-none">Kelola pengguna <i class="bi bi-arrow-right"></i></a></div>
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3"><div class="dashboard-stat p-3"><span class="dashboard-stat__icon text-primary bg-primary-subtle"><i class="bi bi-people"></i></span><div class="dashboard-stat__number">{{ $statistics['total'] }}</div><div class="dashboard-stat__label">Total pengguna</div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="dashboard-stat p-3"><span class="dashboard-stat__icon text-success bg-success-subtle"><i class="bi bi-person-check"></i></span><div class="dashboard-stat__number">{{ $statistics['verified'] }}</div><div class="dashboard-stat__label">Pengguna terverifikasi</div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="dashboard-stat p-3"><span class="dashboard-stat__icon text-warning bg-warning-subtle"><i class="bi bi-person-exclamation"></i></span><div class="dashboard-stat__number">{{ $statistics['pending'] }}</div><div class="dashboard-stat__label">Menunggu verifikasi</div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="dashboard-stat p-3"><span class="dashboard-stat__icon text-secondary bg-secondary-subtle"><i class="bi bi-person-slash"></i></span><div class="dashboard-stat__number">{{ $statistics['inactive'] }}</div><div class="dashboard-stat__label">Pengguna nonaktif</div></div></div>
        </div>
    @else
        <div class="dashboard-status mb-4"><i class="bi bi-shield-check dashboard-status__icon"></i><div><strong>Akun Anda aktif</strong><div class="small text-muted">Anda dapat menggunakan seluruh fitur yang tersedia untuk role Anda.</div></div></div>
    @endif

    <div class="row g-3"><div class="col-lg-7"><x-adminlte-card title="Aktivitas workspace" icon="bi bi-activity" theme="primary"><div class="d-flex align-items-center gap-3"><i class="bi bi-check-circle-fill text-success fs-4"></i><div><strong>Sistem berjalan normal</strong><p class="text-muted mb-0 small">Data dan autentikasi aplikasi siap digunakan.</p></div></div></x-adminlte-card></div><div class="col-lg-5"><x-adminlte-card title="Akses cepat" icon="bi bi-lightning" theme="warning"><div class="d-flex flex-wrap gap-2"><a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-person me-1"></i>Edit profile</a>@if ($statistics)<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i>Pengguna</a>@endif</div></x-adminlte-card></div></div>
@stop
