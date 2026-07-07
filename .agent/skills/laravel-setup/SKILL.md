---
name: laravel-setup
description: Panduan instalasi proyek Laravel 10, konfigurasi lingkungan (.env), dan setup autentikasi menggunakan Laravel Breeze (Blade + Tailwind CSS) untuk TokoKita.
---

# Panduan Instalasi & Setup Awal Laravel 10 - TokoKita

Panduan ini mendokumentasikan langkah-langkah terstandardisasi untuk memasang dan mengonfigurasi proyek Laravel 10 dari awal, termasuk pengaturan database serta otentikasi siap pakai.

---

## 1. Persyaratan Lingkungan (Environment Prerequisites)
* **Versi PHP**: PHP 8.1 hingga 8.3 (Laravel 10 memerlukan minimal PHP 8.1).
* **Ekstensi PHP Wajib**: `pdo_mysql`, `mbstring`, `openssl`, `xml`, `bcmath`, `curl`, `zip`.
* **Composer**: Versi 2.x.
* **Node.js & NPM**: Node 18.x atau lebih baru untuk kompilasi aset frontend.

---

## 2. Langkah Pembuatan Proyek Baru
Untuk menginisialisasi proyek Laravel 10 secara bersih:
```bash
composer create-project laravel/laravel:^10.0 toko-umkm-app
```

---

## 3. Konfigurasi Database di Berkas `.env`
Sesuaikan berkas `.env` di direktori root proyek untuk menghubungkan aplikasi ke database MySQL `tokokita` yang dikonfigurasi menggunakan charset `utf8mb4` dan collation `utf8mb4_unicode_ci`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tokokita
DB_USERNAME=root
DB_PASSWORD=
```

*Catatan Laragon/XAMPP*: Pastikan password disesuaikan (kosong secara default pada Laragon/XAMPP).

---

## 4. Pemasangan Autentikasi (Laravel Breeze)
Kami menggunakan **Laravel Breeze** dengan stack **Blade + Tailwind CSS** sebagai sistem autentikasi bawaan.

### Langkah 1: Unduh Paket Breeze
Jalankan composer untuk memasang paket Breeze di lingkungan developer:
```bash
composer require laravel/breeze --dev
```

### Langkah 2: Jalankan Perintah Instalasi Breeze
Jalankan perintah instalasi Breeze dan pilih opsi **Blade** saat diminta:
```bash
php artisan breeze:install blade
```

*   **Pilihan Opsi Prompt**:
    *   *Dark mode support*: Yes / No (sesuai kebutuhan desain).
    *   *Testing framework*: Pest / PHPUnit (pilih PHPUnit).

### Langkah 3: Kompilasi Aset Frontend (NPM)
Pasang pustaka Tailwind CSS dan kompilasi aset frontend:
```bash
npm install
npm run dev
```

---

## 5. Migrasi dan Seeding
Setelah Breeze terinstal, jalankan migrasi tabel bawaan Breeze beserta data seed:
```bash
php artisan migrate --seed
```
Autentikasi login kini siap digunakan di alamat `/login` dan pendaftaran akun di `/register`.
