# Workflow Pengisian Tabel Summary Penjualan Laporan

Workflow ini memandu agen atau pengembang untuk melakukan kalkulasi ulang (*recalculate*) dan mengisi tabel summary `daily_sales_summaries` berdasarkan data riwayat transaksi riil yang berada di tabel `orders` menggunakan kueri SQL.

---

## 1. Tujuan
Membangun ulang data agregasi harian (`daily_sales_summaries`) tanpa perlu melakukan seeding ulang seluruh database, berguna saat terdapat pembaruan status transaksi pada tabel `orders`.

## 2. Kueri SQL Inti (Raw SQL)

Untuk membersihkan dan mempopulasikan kembali data summary secara bersih, jalankan kueri SQL berikut pada client database Anda (misal: phpMyAdmin, DBeaver, HeidiSQL, atau Laragon Database Manager):

```sql
-- Langkah 1: Bersihkan data summary lama
TRUNCATE TABLE daily_sales_summaries;

-- Langkah 2: Agregasikan data dari tabel orders dan masukkan ke tabel summary
INSERT INTO daily_sales_summaries (store_id, date, total_sales, order_count, created_at, updated_at)
SELECT 
    store_id, 
    DATE(created_at) as date, 
    SUM(final_amount) as total_sales, 
    COUNT(id) as order_count,
    NOW() as created_at,
    NOW() as updated_at
FROM orders
WHERE status = 'completed'
GROUP BY store_id, DATE(created_at);
```

---

## 3. Menjalankan via Command Line (CLI)

Jika Anda ingin menjalankan pembaruan ini secara instan dari terminal VS Code Anda, gunakan perintah **Artisan Tinker** berikut:

```bash
php artisan tinker --execute="DB::statement('TRUNCATE TABLE daily_sales_summaries'); DB::statement('INSERT INTO daily_sales_summaries (store_id, date, total_sales, order_count, created_at, updated_at) SELECT store_id, DATE(created_at), SUM(final_amount), COUNT(id), NOW(), NOW() FROM orders WHERE status = \'completed\' GROUP BY store_id, DATE(created_at)');"
```

---

## 4. Verifikasi Pengisian
Setelah perintah di atas sukses dijalankan, verifikasi bahwa data summary telah terisi dengan memanggil skrip cek database:
```bash
php database-check.php
```
Pastikan baris `daily_sales_summaries` menunjukkan jumlah records yang sesuai dengan sebaran tanggal pesanan selesai (*completed*).
