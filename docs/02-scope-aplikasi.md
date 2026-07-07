# Batasan Scope Aplikasi - TokoKita (E-Commerce UMKM)

Dokumen ini mendefinisikan batasan ruang lingkup (scope) yang realistis dan komprehensif untuk pengerjaan proyek skripsi dalam jangka waktu 1 semester (sekitar 4-6 bulan). Batasan ini dirancang agar sistem memiliki kompleksitas akademis yang memadai (modul relasional kompleks & sistem pelaporan multi-format) namun tetap terukur dan dapat diselesaikan tepat waktu tanpa *scope creep*.

---

## 1. Lingkup Pekerjaan (In-Scope)

Aplikasi **TokoKita** difokuskan pada penyediaan platform e-commerce multi-seller (UMKM) lokal dengan alur kerja pemrosesan transaksi konvensional secara mandiri/independen.

### A. Fitur & Modul Utama
1. **Autentikasi & Otorisasi**:
   * Sistem registrasi & login pengguna terenkripsi menggunakan Laravel Auth.
   * Pemisahan hak akses dinamis dengan Route Middleware berdasarkan 3 role (`admin`, `penjual`, `pembeli`).
2. **Manajemen Produk & Varian**:
   * CRUD produk dasar oleh penjual.
   * Hubungan relasional *One-to-Many* antara tabel `products` dan `product_variants` untuk mendukung variasi harga dan stok (misal: ukuran, warna, paket buku).
   * Perhitungan harga coret otomatis berdasarkan input persentase diskon.
3. **Manajemen Profil & Jam Operasional UMKM**:
   * Setiap penjual dapat merubah deskripsi toko, alamat fisik, nomor kontak, mengunggah file logo toko, dan menentukan cakupan wilayah kirim kurir.
   * Jam operasional buka-tutup toko membatasi pembeli untuk memesan di luar jam aktif.
4. **Manajemen Keranjang & Checkout**:
   * Pembeli dapat memasukkan beberapa item dari produk toko yang sama ke keranjang belanja.
   * Proses kalkulasi subtotal keranjang secara real-time.
   * Validasi klaim kode kupon voucher (potongan flat / potongan persen).
   * Input detail alamat penerima, nama penerima, nomor telepon penerima, dan pilihan kurir ekspedisi pengiriman (JNE, J&T, SiCepat, GoSend) beserta penyesuaian tarif otomatis.
5. **Manajemen Transaksi & Pembayaran Manual**:
   * Pembuatan nomor pesanan otomatis: `ORD-YYYYMMDD-[RANDOM-STRING]`.
   * Sistem pembayaran menggunakan metode **Transfer Bank Manual**. Pembeli diwajibkan mengunggah file gambar bukti transfer sebagai syarat konfirmasi pesanan.
   * Penjual memeriksa bukti transfer secara visual di detail pesanan dan melakukan persetujuan konfirmasi pembayaran yang memicu perubahan status pesanan menjadi `processing`.
6. **Manajemen Pengiriman (Tracking)**:
   * Penjual menginput nomor resi resmi ekspedisi pengiriman barang yang memicu perubahan status pesanan menjadi `shipped`.
   * Pencatatan riwayat perubahan status di tabel `order_histories` sebagai penanda linimasa pelacakan pengiriman bagi pembeli.
7. **Ulasan Produk & Moderasi Konten**:
   * Pembeli memberikan rating (1-5 bintang) dan komentar ulasan untuk produk yang dibeli setelah status pesanan dinyatakan `completed`.
   * Admin memiliki wewenang memoderasi ulasan yang tidak pantas (menyembunyikan ulasan dan menginput catatan alasan moderasi).
8. **Pengajuan Retur Komplain**:
   * Pembeli dapat mengajukan retur untuk barang rusak pada pesanan berstatus `shipped` atau `completed` dengan mengunggah alasan komplain dan foto bukti kerusakan.
   * Admin bertindak sebagai mediator untuk menyetujui atau menolak pengajuan retur barang tersebut.

### B. Daftar Rinci 10+ Jenis Laporan
Aplikasi wajib memuat minimal 10 jenis pelaporan dalam berbagai format (PDF, Excel/CSV, Grafik, dan Dashboard KPI):
1. **Invoice Pesanan (Format PDF)**: Dokumen resmi pembelian bagi pembeli berisi rincian produk, subtotal belanja, diskon voucher, biaya ongkir kurir, status pembayaran bank, dan alamat kirim.
2. **Surat Jalan Pengiriman (Format PDF)**: Dokumen packing slip lampiran paket kurir bagi penjual berisi detail alamat pengirim/penerima dan daftar kuantitas barang.
3. **Laporan Stok Produk (Format PDF)**: Laporan ketersediaan barang aktif toko berisi data produk, kategori, harga dasar, diskon, harga final, jumlah stok (termasuk variasi), dan indikator kritis stok.
4. **Excel Rekap Penjualan Berkala (Format CSV)**: Ekspor data analitis omzet harian toko (tanggal, jumlah transaksi sukses, total belanja bruto, total diskon kupon, total biaya kirim, dan omzet bersih).
5. **Excel Ekspor Data Pesanan & Pembeli (Format CSV)**: Ekspor data transaksional berisi nomor order, tanggal transaksi, nama/email/telepon pembeli, alamat penerima, kurir ekspedisi, status pembayaran, dan total belanja.
6. **Grafik Tren Penjualan per Periode (Line Chart)**: Visualisasi pergerakan grafik omzet harian toko dalam 30 hari terakhir.
7. **Grafik Produk Terlaris (Horizontal Bar Chart)**: Visualisasi 5 produk dengan jumlah kuantitas unit terjual terbanyak.
8. **Grafik Performa Penjual (Vertical Bar Chart)**: Visualisasi perbandingan total omzet bersih (IDR) antar toko/UMKM untuk monitoring admin utama.
9. **Bagan Status Pesanan (Funnel Status Flow)**: Bagan alur pelacakan jumlah pesanan di setiap status transaksi (Pending, Diproses, Dikirim, Selesai, Retur, Batal).
10. **Dashboard Ringkasan KPI Real-Time**: Ringkasan nilai KPI pada dashboard penjual & admin (total omzet toko, jumlah transaksi masuk, jumlah koleksi produk, jumlah verifikasi tertunda).
11. **Grafik Analisis Rating & Ulasan Produk (Bar Chart)**: Visualisasi grafik sebaran penilaian bintang 1 hingga 5 dari ulasan produk pembeli.

---

## 2. Di Luar Lingkup Pekerjaan (Out-of-Scope)

Untuk menjaga fokus riset skripsi pada logika inti modul e-commerce dan sistem pelaporan, fitur-fitur pendukung berikut **dikecualikan** dari pengerjaan:
1. **Payment Gateway Integration (Midtrans/Xendit)**: Verifikasi pembayaran bank disederhanakan menggunakan unggah bukti transfer gambar manual yang dikonfirmasi langsung secara visual oleh penjual.
2. **Real RajaOngkir API Integration**: Tarif pengiriman kurir disimulasikan secara dinamis menggunakan database lookup tarif flat per wilayah/kurir pengiriman.
3. **Real Kurir Tracking API Integration**: Pelacakan posisi kurir disimulasikan menggunakan log input riwayat manual oleh penjual melalui form status pesanan.
4. **Chatting System Real-time (Socket.io/Pusher)**: Komunikasi antara pembeli dan penjual menggunakan integrasi tombol redirect tautan WhatsApp API menggunakan nomor telepon yang terdaftar pada profil toko.
5. **Multi-Currency & Multi-Language**: Aplikasi hanya mendukung transaksi menggunakan mata uang Rupiah (IDR) dan antarmuka berbahasa Indonesia.

---

## 3. Struktur Tabel Database Relasional

Aplikasi menggunakan skema database MySQL dengan 13 tabel relasional inti yang terstandarisasi:
1. `users` (Menyimpan data akun pengguna & peran role: admin, penjual, pembeli).
2. `stores` (Menyimpan profil UMKM, jam operasional, cakupan area kirim. Berelasi *One-to-One* ke `users`).
3. `categories` (Kategori produk: Buku, Fashion, Elektronik, dll).
4. `products` (Menyimpan data produk dasar, harga base, diskon. Berelasi *Many-to-One* ke `stores` & `categories`).
5. `product_variants` (Menyimpan variasi nama, tambahan harga, dan stok varian. Berelasi *Many-to-One* ke `products`).
6. `vouchers` (Menyimpan kupon potongan harga. Berelasi opsional ke `stores`).
7. `carts` (Keranjang belanja pembeli sebelum checkout. Berelasi ke `users`, `products`, `product_variants`).
8. `orders` (Data induk pesanan belanja, alamat kirim, kurir. Berelasi ke `users` (buyer), `stores`, `vouchers`).
9. `order_items` (Detail item barang yang dibeli beserta harga beli histori. Berelasi ke `orders`, `products`, `product_variants`).
10. `payments` (Pencatatan data pembayaran transfer bank & file bukti transfer. Berelasi *One-to-One* ke `orders`).
11. `reviews` (Rating & ulasan produk pembeli. Berelasi ke `order_items`, `products`, `users`).
12. `order_histories` (Log pelacakan alur status order. Berelasi *Many-to-One* ke `orders`).
13. `order_returns` (Data komplain retur barang rusak pembeli. Berelasi *Many-to-One* ke `orders`).

---

## 4. Rencana Verifikasi Sidang Skripsi

1. **Skenario Pengujian Unit (Unit Testing)**: Menggunakan PHPUnit bawaan Laravel untuk memverifikasi keakuratan kalkulasi diskon voucher dan logika pemotongan/pemulihan stok produk.
2. **Skenario Simulasi E2E (End-to-End Walkthrough)**:
   * **Skenario A (Pembeli)**: Menambahkan produk dengan varian tertentu ke keranjang -> menerapkan kupon voucher -> checkout dengan memilih kurir J&T -> mengunggah bukti transfer dummy -> memantau status pesanan.
   * **Skenario B (Penjual)**: Menerima notifikasi pesanan masuk -> memverifikasi gambar bukti transfer pembeli -> memproses barang -> menginput nomor resi ekspedisi -> mengunduh laporan stok produk (PDF) dan laporan rekap penjualan (CSV).
   * **Skenario C (Admin)**: Memantau visualisasi perbandingan omzet penjualan antar UMKM -> memoderasi ulasan bermasalah -> menguji alur persetujuan retur komplain barang rusak.
