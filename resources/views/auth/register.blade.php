@extends('adminlte::auth.auth-master', ['authType' => 'register'])
@section('auth_body')
    <p class="register-box-msg">Buat akun baru</p>
    @if (session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <a href="{{ route('google.redirect') }}" class="btn btn-outline-dark w-100 mb-3"><i class="bi bi-google me-2"></i>Daftar dengan Google</a>
    <div class="d-flex align-items-center gap-2 mb-3 text-muted small"><hr class="flex-grow-1"><span>atau isi data berikut</span><hr class="flex-grow-1"></div>
    <form action="{{ route('register.store') }}" method="post">
        @csrf
        <div class="input-group mb-3"><input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap" required autofocus><div class="input-group-text"><span class="bi bi-person"></span></div>@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="input-group mb-3"><input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required><div class="input-group-text"><span class="bi bi-envelope"></span></div>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="input-group mb-3"><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required><div class="input-group-text"><span class="bi bi-lock-fill"></span></div>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="input-group mb-3"><input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi password" required><div class="input-group-text"><span class="bi bi-lock-fill"></span></div></div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>
    <div class="text-center mt-3"><a href="{{ route('login') }}">Sudah punya akun? Masuk</a></div>
@endsection
