# Laravel 13 + AdminLTE 4 Starter

Project awal Laravel terbaru dengan dashboard AdminLTE 4, autentikasi session, notifikasi kegagalan login, rate limiting, dan RBAC berbasis role.

## Persyaratan

- PHP **8.3+** (Laravel 13). XAMPP saat ini terdeteksi memakai PHP 8.0.30, sehingga upgrade PHP XAMPP wajib dilakukan sebelum menjalankan aplikasi.
- Composer
- Node.js 18+

## Instalasi

```bash
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000/login`.

## Akun login

- Email: `admin@example.com`
- Password: `password`
- Role: `admin`

## Fitur

- Login/logout dengan regenerasi session.
- Validasi form berbahasa Indonesia.
- Pesan error jika email/password salah.
- Rate limiting: maksimal 5 percobaan per menit per email + IP.
- Role middleware `role:admin` untuk membatasi `/admin/settings`.
- Menu AdminLTE otomatis menyembunyikan menu admin dengan Gate `access-admin`.
- SQLite digunakan sebagai database default agar dapat langsung berjalan tanpa konfigurasi MySQL.

Untuk memakai MySQL, ubah `DB_CONNECTION` dan kredensial database pada `.env`, lalu jalankan ulang migrasi.
