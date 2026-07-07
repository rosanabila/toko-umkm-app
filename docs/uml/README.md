# Katalog Diagram UML - TokoKita (E-Commerce UMKM)

Folder ini berisi rancangan diagram rekayasa perangkat lunak menggunakan format **PlantUML** untuk kebutuhan akademis penulisan naskah skripsi aplikasi **TokoKita**.

---

## 1. Diagram Use Case (Batasan Sistem)
* **File**: [use-case.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/use-case.puml)
* **Aktor Terlibat**: Pengunjung (Guest), Pembeli (Buyer), Penjual (Seller), Admin Sistem.
* **Deskripsi**: Diagram ini mendefinisikan batasan hak akses sistem secara global, menggambarkan bagaimana Guest melakukan pendaftaran, Pembeli melakukan transaksi belanja, Penjual memproses pesanan dan laporan toko, serta Admin mengendalikan moderasi komplain dan konten ulasan.

---

## 2. Daftar Activity Diagram (Alur Kerja Inti)

Berikut adalah 10 Activity Diagram yang menggambarkan alur proses bisnis transaksional dan pengelolaan logis di dalam TokoKita:

### 1. Registrasi Akun
* **File**: [activity-register.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-register.puml)
* **Aktor**: Pengunjung (Guest), Sistem, Database.
* **Deskripsi**: Menunjukkan langkah pendaftaran akun baru, validasi formulir input, penyimpanan data user, serta pembuatan draf template profil toko secara otomatis jika mendaftar sebagai Penjual.

### 2. Login Akun
* **File**: [activity-login.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-login.puml)
* **Aktor**: Pengguna (User), Sistem, Database.
* **Deskripsi**: Menggambarkan alur validasi email/password, pembuatan sesi login baru, dan logika *redirecting* dinamis ke dashboard masing-masing sesuai hak akses role.

### 3. Kelola Keranjang Belanja
* **File**: [activity-cart.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-cart.puml)
* **Aktor**: Pembeli, Sistem, Database.
* **Deskripsi**: Menggambarkan logika penambahan barang ke keranjang belanja, penyeleksian varian, validasi stok minimum produk, pengubahan kuantitas, dan penghapusan item keranjang.

### 4. Checkout Pesanan
* **File**: [activity-checkout.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-checkout.puml)
* **Aktor**: Pembeli, Sistem, Database.
* **Deskripsi**: Menampilkan alur pengelompokan barang per toko, pengisian alamat kirim, penyesuaian tarif pengiriman kurir ekspedisi, validasi voucher diskon, pembuatan invoice order pending, pemotongan stok barang, serta pengosongan isi keranjang belanja.

### 5. Unggah Bukti Pembayaran
* **File**: [activity-upload-receipt.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-upload-receipt.puml)
* **Aktor**: Pembeli, Sistem, Database.
* **Deskripsi**: Menggambarkan proses pembeli mengunggah berkas foto bukti transfer bank (maksimal 2MB), validasi format berkas gambar oleh sistem, penyimpanan berkas ke server, dan perubahan status pembayaran order.

### 6. Verifikasi Pembayaran Manual
* **File**: [activity-confirm-payment.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-confirm-payment.puml)
* **Aktor**: Penjual, Sistem, Database.
* **Deskripsi**: Menunjukkan proses verifikasi mutasi bank manual oleh penjual. Jika valid, penjual mengkonfirmasi bukti transfer tersebut yang memicu status order berubah dari `pending` menjadi `processing` (siap dikemas).

### 7. Ajukan Retur Komplain
* **File**: [activity-request-return.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-request-return.puml)
* **Aktor**: Pembeli, Sistem, Database.
* **Deskripsi**: Alur pengajuan klaim barang rusak atau tidak sesuai dengan mengunggah alasan komplain tertulis dan foto bukti pendukung, serta mengubah status transaksi menjadi `returned`.

### 8. Moderasi Pengajuan Retur
* **File**: [activity-moderate-returns.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-moderate-returns.puml)
* **Aktor**: Admin Sistem, Sistem, Database.
* **Deskripsi**: Menggambarkan peran Admin sebagai penengah komplain. Admin meninjau berkas alasan komplain & foto bukti dari pembeli, menginput catatan keputusan (`admin_notes`), lalu menyetujui (*approve*) atau menolak (*reject*) klaim tersebut.

### 9. Beri Rating & Ulasan Produk
* **File**: [activity-add-review.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-add-review.puml)
* **Aktor**: Pembeli, Sistem, Database.
* **Deskripsi**: Menggambarkan pengunggahan ulasan produk. Pembeli memberikan rating bintang (1-5) dan komentar ulasan untuk produk yang dibeli setelah pesanan selesai, dan sistem menghitung ulang rata-rata rating produk secara dinamis.

### 10. Kelola Produk & Varian
* **File**: [activity-manage-products.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/activity-manage-products.puml)
* **Aktor**: Penjual, Sistem, Database.
* **Deskripsi**: Menggambarkan penambahan produk baru ke toko, pengunggahan gambar produk, pembuatan slug SEO URL otomatis, serta penginputan baris variasi spesifikasi (seperti warna/ukuran) lengkap dengan penyesuaian harga dan stok varian.

---

## 3. Daftar Sequence Diagram (Alur Interaksi MVC Teknis)

Berikut adalah 10 Sequence Diagram yang memetakan Activity Diagram di atas ke dalam arsitektur teknis Laravel MVC (Model-View-Controller) beserta interaksi database MySQL:

### 1. Registrasi Akun
* **File**: [sequence-register.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-register.puml)
* **Deskripsi**: Memodelkan pengiriman data pendaftaran dari `RegisterView` (Blade), diproses oleh `AuthController@register` dengan validasi input, penyimpanan user, pembuatan template store, dan otentikasi sesi otomatis.

### 2. Login Akun
* **File**: [sequence-login.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-login.puml)
* **Deskripsi**: Memodelkan permintaan login dari `LoginView`, diverifikasi oleh `AuthController@login` melalui pengecekan kredensial database, regenerasi sesi, dan pengalihan dinamis ke dashboard berdasarkan role.

### 3. Kelola Keranjang Belanja
* **File**: [sequence-cart.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-cart.puml)
* **Deskripsi**: Memodelkan proses tambah keranjang belanja dari `ProductShow` ke `CartController@add`, memeriksa sisa stok produk/varian ke database, dan mengupdate baris item keranjang.

### 4. Checkout Pesanan
* **File**: [sequence-checkout.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-checkout.puml)
* **Deskripsi**: Memodelkan transaksi checkout melalui `CartController@processCheckout`, validasi kupon voucher belanja, pembuatan baris pesanan (`orders`) melalui `OrderService` dan item (`order_items`), pemotongan kuantitas stok produk, inisialisasi pembayaran pending, log riwayat status, dan pembersihan item keranjang.

### 5. Unggah Bukti Pembayaran
* **File**: [sequence-upload-receipt.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-upload-receipt.puml)
* **Deskripsi**: Memodelkan proses pengunggahan bukti transfer pembeli dari `OrderDetailView` yang diverifikasi format dan ukurannya oleh `BuyerController@submitPayment`, memindahkan berkas gambar, dan memperbarui status pembayaran.

### 6. Verifikasi Pembayaran Manual
* **File**: [sequence-confirm-payment.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-confirm-payment.puml)
* **Deskripsi**: Memodelkan verifikasi pembayaran oleh penjual melalui `SellerController@confirmPayment`, memperbarui status bayar ke `confirmed`, mengubah status order menjadi `processing`, dan mencatat riwayat log order.

### 7. Ajukan Retur Komplain
* **File**: [sequence-request-return.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-request-return.puml)
* **Deskripsi**: Memodelkan pengajuan retur dari `OrderDetailView` ke `BuyerController@submitReturn` untuk order yang sah, validasi input & file gambar, pembuatan entitas `OrderReturn`, dan mengubah status order.

### 8. Moderasi Pengajuan Retur
* **File**: [sequence-moderate-returns.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-moderate-returns.puml)
* **Deskripsi**: Memodelkan tindakan persetujuan/penolakan retur oleh admin melalui `AdminController@moderateReturn`, memodifikasi status pengembalian, menulis catatan admin, dan mengupdate log order.

### 9. Beri Rating & Ulasan Produk
* **File**: [sequence-add-review.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-add-review.puml)
* **Deskripsi**: Memodelkan proses pemberian rating/ulasan dari `OrderDetailView` yang divalidasi kepemilikan dan status ordernya oleh `BuyerController@submitReview`, serta mencegah ulasan ganda.

### 10. Kelola Produk & Varian
* **File**: [sequence-manage-products.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/sequence-manage-products.puml)
* **Deskripsi**: Memodelkan input data produk baru penjual melalui `SellerProductController@store`, validasi form, penyimpanan gambar, pembuatan slug SEO, penyimpanan database `products`, serta iterasi penyimpanan variasi ke `product_variants`.

---

## 4. Class Diagram (Struktur Kelas & Asosiasi Relasional)
* **File**: [class-diagram.puml](file:///c:/laragon/www/toko-umkm-app/docs/uml/class-diagram.puml)
* **Deskripsi**: Diagram kelas komprehensif yang menyintesis seluruh arsitektur TokoKita. Diagram ini menampilkan:
  * **Controllers (Laravel MVC)**: Kelas logika pengolah Request beserta daftar method operasionalnya.
  * **Models (Eloquent ORM)**: Kelas representasi tabel database MySQL lengkap dengan tipe data atribut kolom, method helper bisnis, serta pemetaan relasi kardinalitas (*1-to-1*, *1-to-Many*, *Many-to-Many*).
  * **Dependencies**: Hubungan ketergantungan antara Controllers dan Models yang mereka manipulasikan.
