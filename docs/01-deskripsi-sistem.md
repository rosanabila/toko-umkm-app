# Deskripsi Sistem - TokoKita (E-Commerce UMKM)

Dokumen ini berisi analisis kebutuhan sistem menyeluruh untuk aplikasi **TokoKita**, sebuah platform web e-commerce terintegrasi yang dirancang khusus untuk mendukung digitalisasi UMKM (Usaha Mikro, Kecil, dan Menengah). 

---

## 1. Profil & Arsitektur Sistem

* **Nama Aplikasi**: TokoKita
* **Platform**: Aplikasi Web (Responsive Desktop & Mobile)
* **Teknologi Utama**: Laravel 10, PHP 8.3, MySQL
* **Visual & Style**: Vanilla CSS (dengan HSL variable styling system, desain glassmorphic premium, tipografi modern Inter & Outfit, mikro-animasi transisi, dan Chart.js).
* **Metode Ekspor Laporan**:
  * Dokumen Cetak/Dokumen Resmi: **PDF** (menggunakan `laravel-dompdf`).
  * Dokumen Data Analitik: **Excel/CSV** (menggunakan stream buffer format `.csv` dengan UTF-8 BOM).
  * Laporan Visual: **Interactive Charts & Dashboard** (menggunakan `Chart.js` dan visualisasi step-indicator/funnel).

---

## 2. Analisis Aktor & Hak Akses (Roles)

Sistem ini memiliki 3 aktor dengan peran dan batasan yang terdefinisi dengan jelas:

### A. Pembeli Umum (Buyer)
Pengguna umum yang menggunakan platform untuk mencari dan membeli produk lokal dari berbagai UMKM.
* **Fitur Utama**:
  * Registrasi akun dan login.
  * Menjelajahi katalog produk dengan pencarian, filter kategori, dan rentang harga.
  * Memilih variasi produk (warna, ukuran, paket) saat melihat detail produk.
  * Mengelola keranjang belanja (tambah, edit kuantitas, hapus item).
  * Mengklaim kupon voucher belanja toko/global untuk mendapatkan potongan harga.
  * Melakukan checkout pesanan (mengisi alamat pengiriman, memilih kurir ekspedisi beserta tarif otomatis).
  * Mengunggah bukti transfer pembayaran bank untuk verifikasi manual.
  * Memantau linimasa riwayat status pengiriman pesanan.
  * Mengajukan retur/komplain barang jika barang rusak (wajib mengunggah alasan & foto bukti).
  * Memberikan penilaian rating bintang (1-5) dan ulasan komentar untuk produk yang telah selesai dibeli.

### B. Penjual / Pemilik UMKM (Seller)
Pemilik usaha lokal yang mengoperasikan toko mereka sendiri di dalam platform.
* **Fitur Utama**:
  * Mengelola profil toko (nama, deskripsi, alamat fisik, jam operasional buka/tutup, upload logo toko, dan teks area cakupan wilayah pengiriman).
  * Manajemen Produk (CRUD produk dasar: nama, deskripsi, kategori, harga base, diskon, stok, upload foto produk).
  * Manajemen Varian Produk (menambah, mengubah, atau menghapus variasi tambahan harga dan stok per varian).
  * Manajemen Voucher Toko (CRUD voucher potongan diskon flat atau persentase khusus untuk toko sendiri).
  * Manajemen Pesanan Masuk (melihat daftar pesanan, memproses transaksi, mengkonfirmasi bukti pembayaran transfer bank, menginput nomor resi pengiriman kurir, dan mengubah status order).
  * Dashboard KPI toko (omzet real-time, jumlah produk aktif, jumlah order).
  * Laporan Toko (unduh PDF Laporan Stok Produk, ekspor CSV Rekap Penjualan, ekspor CSV data pesanan/pembeli).
  * Grafik Analisis (tren omzet harian 30 hari terakhir, 5 produk terlaris, sebaran status pesanan, analisis rating & ulasan pembeli).

### C. Admin Sistem (Administrator)
Pengelola platform utama yang memoderasi aktivitas dan menjaga kesehatan sistem e-commerce.
* **Fitur Utama**:
  * Manajemen Akun Pengguna (melihat seluruh user, mengubah hak akses/role secara dinamis).
  * Moderasi Ulasan & Rating (menyembunyikan ulasan bermasalah yang mengandung unsur kata kotor/SARA/spam, dan menulis catatan moderasi).
  * Moderasi Retur Barang (meninjau komplain barang rusak pembeli, melihat bukti foto komplain, lalu menyetujui atau menolak pengajuan retur).
  * Dashboard KPI Global (total omzet seluruh sistem, total pengguna, total toko aktif, total order).
  * Grafik Analisis Global (visualisasi performa penjualan antar toko/UMKM menggunakan perbandingan bar chart, visualisasi status order global).

---

## 3. Analisis Modul Pengelolaan (Modul Inti)

Sistem mengintegrasikan 8 modul pengelolaan utama yang saling terhubung:

1. **Manajemen Produk**: Sinkronisasi data antara tabel kategori, produk dasar, dan varian harga. Sistem menghitung secara dinamis harga setelah potongan diskon persen (`price * (1 - discount/100)`).
2. **Manajemen Toko**: Pengaturan profil UMKM, jam operasional buka-tutup toko, serta area pengiriman.
3. **Manajemen Pesanan**: Pencatatan order menggunakan nomor pesanan unik (`ORD-YYYYMMDD-XXXXX`). Mengatur alur perubahan status: `Pending` (menunggu bayar/verifikasi) -> `Processing` (dikemas/diproses) -> `Shipped` (dikirim dengan nomor resi) -> `Completed` (diterima pembeli) / `Cancelled` (dibatalkan, stok otomatis kembali) / `Returned` (proses retur).
4. **Manajemen Pembayaran**: Pencatatan metode transfer bank, jumlah tagihan, status bayar (`pending`, `confirmed`, `failed`, `refunded`), serta penyimpanan file gambar bukti transfer.
5. **Manajemen Pengguna & Role**: Autentikasi aman dengan pemisahan hak akses menggunakan middleware dinamis (`RoleMiddleware`).
6. **Manajemen Pengiriman**: Integrasi kurir ekspedisi (JNE, J&T, SiCepat, GoSend) dengan kalkulasi tarif dinamis di checkout dan estimasi waktu kirim.
7. **Manajemen Ulasan & Moderasi**: Sistem penyaringan ulasan pembeli demi menjaga integritas produk UMKM dari kompetisi tidak sehat atau ujaran kebencian.
8. **Manajemen Voucher & Promosi**: Penerapan kode kupon diskon (tipe flat/persentase) dengan validasi tanggal kedaluwarsa dan nominal minimal belanja.

---

## 4. Spesifikasi Modul Laporan (Minimal 10 Jenis)

Sistem menyediakan 11 jenis pelaporan terintegrasi untuk kebutuhan akademis skripsi:

| No | Jenis Laporan | Format | Pengguna | Parameter & Deskripsi Data |
|---|---|---|---|---|
| 1 | **Invoice Pesanan** | PDF | Pembeli & Penjual | Rincian detail belanja barang, subtotal, potongan diskon voucher, biaya ongkir kurir, status pembayaran, dan data alamat penerima. |
| 2 | **Surat Jalan Pengiriman** | PDF | Penjual | Dokumen packing slip berisi pengirim (toko), penerima (pembeli), kurir ekspedisi, daftar barang & kuantitas (tanpa nominal harga) serta kolom tanda tangan penerima. |
| 3 | **Laporan Stok Produk** | PDF | Penjual | Daftar seluruh produk toko, kategori, harga dasar, diskon promo, harga final, jumlah total stok (termasuk varian), dan status indikator stok (Aman/Kritis/Habis). |
| 4 | **Rekap Penjualan** | CSV (Excel) | Penjual | Rincian omzet berkala harian/bulanan (tanggal, jumlah order sukses, bruto belanja, total diskon voucher diterapkan, total biaya kirim, dan omzet bersih). |
| 5 | **Ekspor Pesanan & Pembeli** | CSV (Excel) | Penjual | Rekap data pembeli (nama, email, telepon), alamat kirim, kurir, metode bayar, status bayar, status order, dan nominal transaksi. |
| 6 | **Tren Penjualan per Periode** | Grafik (Line) | Penjual | Visualisasi pergerakan omzet harian toko dalam 30 hari terakhir. |
| 7 | **Produk Terlaris** | Grafik (Bar) | Penjual | Visualisasi 5 produk dengan kuantitas unit terjual paling banyak. |
| 8 | **Performa Penjual** | Grafik (Bar) | Admin | Perbandingan total omzet bersih (IDR) antar toko UMKM yang terdaftar di dalam platform. |
| 9 | **Bagan Status Pesanan** | Bagan (Funnel Flow) | Penjual & Admin | Bagan visual jumlah pesanan di setiap status transaksi (Pending, Diproses, Dikirim, Selesai, Retur, Batal). |
| 10| **Dashboard KPI Real-Time** | Dashboard | Penjual & Admin | Ringkasan kartu nilai real-time (Omzet, Total Orders, Produk, Butuh Verifikasi Bayar). |
| 11| **Analisis Rating & Ulasan** | Grafik (Bar) | Penjual | Visualisasi sebaran ulasan bintang 1 hingga bintang 5 yang diperoleh toko. |
