# Database Reset & Seed Verification Workflow

Workflow ini memandu agen untuk melakukan reset database, pengisian data awal (*seeding*), serta memverifikasi kesesuaian data yang telah dibuat.

---

## 1. Prasyarat
* MySQL server berjalan pada host `127.0.0.1`.
* Variabel lingkungan `.env` dikonfigurasi dengan benar (`DB_DATABASE=tokokita`).

## 2. Langkah-Langkah Reset & Seeding

### Langkah 1: Jalankan Fresh Migration dan Seed
Jalankan perintah Artisan berikut untuk membersihkan seluruh tabel lama, membuat struktur tabel baru beserta indeks komposit, dan mempopulasikan data dummy:
```bash
php artisan migrate:fresh --seed
```

### Langkah 2: Verifikasi Jumlah Record Tabel
Jalankan skrip verifikasi PHP yang terletak di root direktori untuk memeriksa jumlah record di setiap tabel:
```bash
php database-check.php
```

### Langkah 3: Bandingkan dengan Hasil yang Diharapkan
Pastikan output pencatatan record memenuhi batasan minimal berikut:
* **users** >= 14 (1 Admin, 3 Penjual, 10 Pembeli)
* **stores** >= 3 (1 store per penjual)
* **categories** >= 5
* **products** >= 50
* **orders** >= 100

*Contoh Output Sukses:*
```text
====================================
 TOKOKITA DATABASE RECORD COUNT      
====================================
users                    : 14 records
stores                   : 3 records
categories               : 5 records
products                 : 50 records
product_variants         : 29 records
vouchers                 : 4 records
orders                   : 105 records
order_items              : 156 records
payments                 : 105 records
reviews                  : 38 records
order_histories          : 315 records
order_returns            : 13 records
carts                    : 0 records
category_product (pivot) : 50 records
daily_sales_summaries    : 45 records
====================================
```
Jika ada tabel dengan record `0` (kecuali `carts` yang memang kosong di awal transaksi baru), periksa `database/logs/laravel.log` untuk mendeteksi kegagalan parsing data.
