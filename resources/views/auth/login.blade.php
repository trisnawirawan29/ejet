@extends('adminlte::auth.auth-master', ['authType' => 'login'])

@push('css')
<style>
    body.login-page { background: #edf2f7 !important; color: #172033; }
    .login-page .login-box { width: min(960px, calc(100% - 2rem)); }
    .login-page .card { overflow: hidden; border: 0; border-radius: 18px; box-shadow: 0 24px 60px rgba(15, 23, 42, .14); }
    .login-page .card-header { padding: 1.35rem 2rem; border: 0; background: #fff; }
    .login-page .card-header a { color: #172033; font-size: 1.7rem; font-weight: 700; }
    .login-page .card-body { padding: 0; }
    .login-modern-grid { display: grid; grid-template-columns: .9fr 1.1fr; min-height: 470px; }
    .login-intro { position: relative; display: flex; flex-direction: column; justify-content: space-between; padding: 2.5rem; color: #fff; background: #172033; }
    .login-intro::after { position: absolute; right: 2rem; bottom: 2rem; width: 110px; height: 110px; border: 1px solid rgba(255,255,255,.18); border-radius: 50%; content: ''; }
    .login-intro__eyebrow { margin-bottom: 1.25rem; color: #8ee0c2; font-size: .75rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .login-intro h1 { max-width: 310px; margin-bottom: 1rem; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.05; }
    .login-intro p { max-width: 330px; margin: 0; color: #d5e6df; line-height: 1.7; }
    .login-intro__footer { z-index: 1; display: flex; align-items: center; gap: .75rem; color: #bdc7d8; font-size: .85rem; }
    .login-status-dot { width: 9px; height: 9px; border-radius: 50%; background: #58d6aa; box-shadow: 0 0 0 5px rgba(88,214,170,.13); }
    .login-form-panel { padding: 2.5rem clamp(1.5rem, 5vw, 4rem); background: #fff; }
    .login-form-panel__top { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 2rem; }
    .login-form-panel h2 { margin: 0 0 .4rem; color: #172033; font-size: 1.65rem; }
    .login-form-panel__top p { margin: 0; color: #52635f; }
    .login-clock { color: #52635f; font-size: .8rem; text-align: right; white-space: nowrap; }
    .login-field { position: relative; margin-bottom: 1.1rem; }
    .login-field .login-label { display: block; margin-bottom: .45rem; color: #344943; font-size: .78rem; font-weight: 700; }
    .login-field .bi { position: absolute; top: calc(1.55rem + 25px); left: 1rem; z-index: 1; color: #52635f; transform: translateY(-50%); }
    .login-field .form-control { min-height: 52px; padding-left: 2.75rem; border-color: #cbd8d3; border-radius: 10px; color: #172033 !important; background-color: #fff; -webkit-text-fill-color: #172033; }
    .login-field .form-control::placeholder { color: #687873; opacity: 1; }
    .login-field .form-control:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #fff inset; -webkit-text-fill-color: #172033; }
    .login-field .form-control:focus { border-color: #399d7d; box-shadow: 0 0 0 .2rem rgba(57,157,125,.12); }
    .login-field .password-toggle { position: absolute; top: 50%; right: .7rem; z-index: 2; padding: .35rem .5rem; border: 0; color: #718096; background: transparent; transform: translateY(-50%); }
    .login-field .password-toggle:hover { color: #172033; }
    .login-form-panel .form-check-label { color: #52635f; font-size: .9rem; }
    .login-submit { min-height: 52px; border: 0; border-radius: 10px; background: #238565; font-weight: 600; transition: transform .2s ease, background .2s ease; }
    .login-submit:hover { background: #1b6d53; transform: translateY(-1px); }
    .login-register { margin-top: 1.5rem; color: #52635f; font-size: .9rem; text-align: center; }
    .login-register a { color: #176246; font-weight: 700; text-decoration: none; }
    .login-demo { margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #dfe8e4; color: #687873; font-size: .75rem; text-align: center; }
    @media (max-width: 700px) {
        body.login-page { background: #f3f7f5 !important; }
        .login-page .login-box { width: min(520px, calc(100% - 1rem)); margin: .5rem auto; }
        .login-page .card { border-radius: 16px; box-shadow: 0 14px 35px rgba(15, 23, 42, .1); }
        .login-page .card-header { display: none; }
        .login-modern-grid { display: block; min-height: 0; }
        .login-intro { min-height: 205px; padding: 1.75rem 1.5rem 1.5rem; }
        .login-intro__eyebrow { margin-bottom: 1rem; color: #9be0c5; font-size: .68rem; }
        .login-intro h1 { max-width: 275px; margin-bottom: .75rem; font-size: 1.8rem; }
        .login-intro p { display: block; max-width: 290px; color: #d5e6df; font-size: .86rem; line-height: 1.55; }
        .login-intro__footer { font-size: .75rem; }
        .login-intro::after { right: 1rem; bottom: 1rem; width: 75px; height: 75px; }
        .login-form-panel { padding: 1.5rem 1.25rem 1.75rem; }
        .login-form-panel__top { margin-bottom: 1.35rem; }
        .login-clock { display: none; }
        .login-form-panel h2 { font-size: 1.45rem; }
        .login-form-panel__top p { color: #52635f; font-size: .84rem; }
        .login-field { margin-bottom: 1rem; }
        .login-field .login-label { color: #344943; font-size: .76rem; }
        .login-field .form-control { min-height: 50px; }
        .login-submit { min-height: 50px; }
        .login-form-panel .form-check-label { font-size: .82rem; }
        .login-form-panel .small.text-muted { color: #52635f !important; font-size: .72rem; }
    }
</style>
@endpush

@section('auth_body')
    <div class="login-modern-grid">
        <section class="login-intro" aria-label="Informasi aplikasi">
            <div>
                <div class="login-intro__eyebrow">{{ $applicationName }}</div>
                <h1>Selamat datang kembali.</h1>
                <p>{{ $applicationTagline }}</p>
            </div>
            <div class="login-intro__footer"><span class="login-status-dot"></span><span>Sistem siap digunakan</span></div>
        </section>
        <section class="login-form-panel">
            <div class="login-form-panel__top">
                <div><h2>Masuk ke akun</h2><p id="loginGreeting">Akses workspace Anda.</p></div>
                <time class="login-clock" id="loginClock" aria-label="Waktu saat ini"></time>
            </div>
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if (session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            <a href="{{ route('google.redirect') }}" class="btn btn-outline-dark w-100 mb-3"><i class="bi bi-google me-2"></i>Lanjutkan dengan Google</a>
            <div class="d-flex align-items-center gap-2 mb-3 text-muted small"><hr class="flex-grow-1"><span>atau masuk dengan email</span><hr class="flex-grow-1"></div>
            <form action="{{ route('login.store') }}" method="post">
                @csrf
                <div class="login-field"><i class="bi bi-envelope" aria-hidden="true"></i><label class="login-label" for="login-email">Email atau username</label><input id="login-email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email Anda" autocomplete="email" required autofocus>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="login-field"><i class="bi bi-lock" aria-hidden="true"></i><label class="login-label" for="login-password">Password</label><input id="login-password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" class="password-toggle" id="passwordToggle" title="Tampilkan password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="d-flex justify-content-between align-items-center mb-4"><div class="form-check"><input type="checkbox" name="remember" class="form-check-input" id="remember"><label class="form-check-label" for="remember">Ingat saya</label></div><span class="small text-muted">Akun aman & terenkripsi</span></div>
                <button type="submit" class="btn btn-primary login-submit w-100">Masuk ke workspace <i class="bi bi-arrow-right ms-1"></i></button>
            </form>
            <div class="login-register">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></div>
            <div class="login-demo">Akun demo: admin@example.com / password</div>
        </section>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const clock = document.getElementById('loginClock');
    const greeting = document.getElementById('loginGreeting');
    const updateTime = () => { const now = new Date(); clock.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); greeting.textContent = now.getHours() < 12 ? 'Selamat pagi, siap memulai hari?' : now.getHours() < 18 ? 'Selamat siang, lanjutkan pekerjaan Anda.' : 'Selamat malam, mari selesaikan hari ini.'; };
    updateTime(); setInterval(updateTime, 60000);
    const toggle = document.getElementById('passwordToggle');
    const password = document.getElementById('login-password');
    toggle.addEventListener('click', () => { const visible = password.type === 'text'; password.type = visible ? 'password' : 'text'; toggle.title = visible ? 'Tampilkan password' : 'Sembunyikan password'; toggle.setAttribute('aria-label', toggle.title); toggle.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash'; });
});
</script>
@endpush
