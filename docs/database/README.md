# Dokumentasi Relasi Database - TokoKita (E-Commerce UMKM)

Berkas ini mendokumentasikan skema relasi antar tabel (ERD) untuk aplikasi **TokoKita**, yang ditulis dalam format **DBML (Database Markup Language)** di berkas [erd.dbml](file:///c:/laragon/www/toko-umkm-app/docs/database/erd.dbml).

---

## 1. Daftar Tabel & Peran Bisnis

Database MySQL TokoKita mengintegrasikan 15 tabel relasional inti yang membagi data transaksional, data katalog, dan data pengguna:

1. **`users`**: Menyimpan kredensial login (Email & Password), WhatsApp, dan pengelompokan tingkat hak akses pengguna (`admin`, `penjual`, `pembeli`).
2. **`stores`**: Menyimpan data profil toko UMKM (Nama, alamat, operasional jam, area pengiriman). Berelasi satu-ke-satu dengan tabel `users` (penjual).
3. **`categories`**: Klasifikasi kategori produk yang diatur oleh Admin (misal: Buku, Fashion, Elektronik).
4. **`category_product`**: Tabel pivot Banyak-ke-Banyak (*Many-to-Many*) untuk memetakan produk ke beberapa kategori secara ter-normalisasi (1NF).
5. **`products`**: Data dasar produk yang dijual oleh masing-masing toko. Menyimpan harga dasar, diskon persentase, gambar, dan deskripsi produk.
6. **`product_variants`**: Menyimpan variasi spesifikasi dari produk (misal: warna, ukuran) beserta tambahan harga (*price adjustment*) dan stok masing-masing.
7. **`vouchers`**: Kupon potongan harga belanja (flat/persen) yang diterbitkan toko sendiri atau global oleh Admin.
8. **`carts`**: Menyimpan daftar keranjang belanja sementara pembeli sebelum masuk proses checkout.
9. **`orders`**: Menyimpan data transaksi induk belanja, nomor order unik (`ORD-YYYYMMDD-XXXXX`), status order, detail kurir ekspedisi, ongkos kirim, dan alamat pengiriman.
10. **`order_items`**: Menyimpan rincian item barang yang dibeli di setiap transaksi beserta data harga dan diskon saat transaksi terjadi (*snapshot history*).
11. **`payments`**: Menyimpan data transfer bank manual, berkas foto bukti transfer pembeli, status pembayaran, dan data admin/penjual yang memverifikasi.
12. **`reviews`**: Menyimpan ulasan rating (bintang 1-5) dan komentar pembeli untuk produk yang sudah selesai dibeli.
13. **`order_histories`**: Log linimasa pelacakan riwayat alur status pesanan belanja dari awal hingga akhir.
14. **`order_returns`**: Pencatatan data komplain klaim barang rusak oleh pembeli (alasan, foto bukti) beserta keputusan persetujuan dari Admin.
15. **`wishlists`**: Menyimpan daftar keinginan (Wishlist) produk oleh pembeli. Menghubungkan pengguna dengan produk dalam hubungan Banyak-ke-Banyak (N:M).

---

## 2. Penjelasan Relasi & Referensi Hubungan (Foreign Keys)

### A. Hubungan Satu-ke-Satu (One-to-One)
* **`stores.user_id - users.id`**
  * *Penjelasan*: Satu akun pengguna role `penjual` hanya memiliki maksimal satu toko UMKM. Jika user dihapus, profil toko ikut terhapus (*Cascade*).
* **`payments.order_id - orders.id`**
  * *Penjelasan*: Setiap transaksi pesanan (`orders`) hanya berpasangan dengan satu formulir data bukti pembayaran (`payments`).
* **`reviews.order_item_id - order_items.id`**
  * *Penjelasan*: Setiap baris item belanjaan di dalam pesanan hanya boleh diberi ulasan ulasan (`reviews`) sebanyak satu kali saja (mencegah rating ganda).

### B. Hubungan Satu-ke-Banyak (One-to-Many / Many-to-One)
* **`products.store_id > stores.id`**
  * *Penjelasan*: Satu toko UMKM dapat memiliki banyak produk (`>`).
* **`category_product.product_id > products.id` & `category_product.category_id > categories.id`**
  * *Penjelasan*: Relasi Banyak-ke-Banyak (*Many-to-Many*) antara produk dan kategori yang dimediasikan oleh tabel pivot `category_product` untuk memetakan pengelompokan ganda yang dinamis dan memenuhi 1NF.
  * *Aturan Hapus*: Penghapusan produk atau kategori akan memicu penghapusan baris data relasi pivot secara otomatis (*Cascade*).
* **`product_variants.product_id > products.id`**
  * *Penjelasan*: Satu produk utama memiliki banyak variasi warna/ukuran. Jika produk dihapus, variannya otomatis terhapus (*Cascade*).
* **`vouchers.store_id > stores.id`**
  * *Penjelasan*: Satu voucher belanja diterbitkan oleh satu toko UMKM. Kolom `store_id` dapat bernilai `NULL` jika voucher bersifat global (diterbitkan Admin dan bisa diklaim di toko manapun).
* **`carts.user_id > users.id`, `carts.product_id > products.id`, `carts.product_variant_id > product_variants.id`**
  * *Penjelasan*: Keranjang belanja mengaitkan pengguna ke produk dan variasi yang dipilih.
* **`orders.buyer_id > users.id` & `orders.store_id > stores.id`**
  * *Penjelasan*: Satu pembeli dapat melakukan banyak transaksi order. Satu toko menerima banyak pesanan dari berbagai pembeli.
  * *Aturan Hapus*: Penghapusan user/toko dibatasi (*Restrict*) jika data transaksinya masih ada demi integritas arsip laporan keuangan.
* **`order_items.order_id > orders.id`**
  * *Penjelasan*: Satu transaksi pesanan memuat banyak item barang belanjaan di dalam tabel `order_items`.
* **`order_histories.order_id > orders.id`**
  * *Penjelasan*: Satu pesanan memiliki riwayat status bertahap (diproses, dikirim, selesai) yang dicatat di tabel history log.
* **`order_returns.order_id > orders.id`**
  * *Penjelasan*: Satu pesanan dikaitkan dengan pelaporan komplain retur barang rusak.
* **`wishlists.user_id > users.id` & `wishlists.product_id > products.id`**
  * *Penjelasan*: Menghubungkan pengguna pembeli dengan produk yang mereka sukai (Banyak-ke-Banyak). Penghapusan akun pengguna atau produk akan memicu penghapusan entri wishlist secara otomatis (*Cascade*).

---

## 3. Penerapan Konvensi Laravel & Soft Deletes

Untuk memenuhi standar keamanan dan integritas audit skripsi, beberapa tabel penting menerapkan fitur **Soft Deletes** (`deleted_at`):
* **Tabel yang menggunakan Soft Deletes**: `users`, `stores`, `products`, dan `vouchers`.
* *Tujuan*: Ketika produk atau voucher dihapus oleh penjual, data tidak benar-benar hilang dari database. Hal ini menjamin bahwa relasi pada data transaksi masa lalu (`order_items` dan `orders`) tetap utuh dan tidak menyebabkan error *broken reference* atau ketidakseimbangan laporan keuangan berkala.
