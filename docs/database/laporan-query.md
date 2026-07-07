# Analisis Kebutuhan Query Laporan & Optimasi Database

Dokumen ini berisi analisis kebutuhan query SQL untuk 11 jenis laporan yang diintegrasikan dalam aplikasi **TokoKita**, serta menentukan indeks komposit tambahan dan tabel summary untuk mengoptimalkan kinerja database MySQL.

---

## 1. Analisis Query & Kebutuhan Indeks per Laporan

### 1. Invoice Pesanan (PDF)
* **Deskripsi**: Menampilkan rincian detail satu pesanan.
* **SQL Query**:
  ```sql
  SELECT * FROM orders WHERE id = ? LIMIT 1;
  SELECT * FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = ?;
  SELECT * FROM payments WHERE order_id = ? LIMIT 1;
  ```
* **Kebutuhan Indeks**: Indeks default Kunci Primer (PK) `orders.id` dan Kunci Asing (FK) `order_items.order_id` sudah memadai.

### 2. Surat Jalan Pengiriman (PDF)
* **Deskripsi**: Informasi alamat kirim, penerima, kurir, dan daftar kuantitas barang.
* **SQL Query**:
  ```sql
  SELECT order_number, shipping_address, shipping_recipient_name, shipping_recipient_phone, shipping_courier FROM orders WHERE id = ?;
  SELECT quantity, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = ?;
  ```
* **Kebutuhan Indeks**: Memanfaatkan Kunci Asing `order_items.order_id` yang sudah terindeks default.

### 3. Laporan Stok Produk (PDF)
* **Deskripsi**: Daftar seluruh stok produk aktif toko beserta variannya.
* **SQL Query**:
  ```sql
  SELECT id, name, price, discount_percent, stock FROM products WHERE store_id = ? AND deleted_at IS NULL;
  SELECT id, name, price_adjustment, stock FROM product_variants WHERE product_id IN (?);
  ```
* **Optimasi Indeks**: Kueri menyaring data berdasarkan `store_id` dan memeriksa soft delete `deleted_at`.
* **Rekomendasi Indeks**: Indeks komposit pada `products (store_id, deleted_at)`.

### 4. Rekap Penjualan Berkala (CSV) & 6. Tren Penjualan per Periode (Line Chart)
* **Deskripsi**: Laporan omzet berkala harian/mingguan/bulanan berdasarkan tanggal.
* **SQL Query**:
  ```sql
  SELECT DATE(created_at) as date, SUM(final_amount) as total_sales, COUNT(id) as order_count 
  FROM orders 
  WHERE store_id = ? AND status = 'completed' AND created_at BETWEEN ? AND ? 
  GROUP BY DATE(created_at);
  ```
* **Optimasi**: Melakukan agregasi data transaksi (`SUM` dan `COUNT`) pada rentang tanggal tertentu. Pada data jutaan baris, kueri ini sangat lambat.
* **Rekomendasi**:
  * **Tabel Summary**: `daily_sales_summaries` untuk menyimpan pra-agregasi omzet harian per toko.
  * **Indeks Komposit**: Indeks pada `orders (store_id, status, created_at)` untuk mempercepat penarikan data transaksi ter-filter.

### 5. Ekspor Data Pesanan & Pembeli (CSV)
* **Deskripsi**: Daftar pelanggan, kurir, status bayar, dan nominal pesanan toko.
* **SQL Query**:
  ```sql
  SELECT o.order_number, o.created_at, u.name, u.email, o.shipping_courier, p.status, o.final_amount 
  FROM orders o 
  JOIN users u ON o.buyer_id = u.id 
  LEFT JOIN payments p ON o.id = p.order_id 
  WHERE o.store_id = ? AND o.created_at BETWEEN ? AND ?;
  ```
* **Optimasi Indeks**: Menyaring pesanan per toko berdasarkan rentang tanggal `created_at`.
* **Rekomendasi Indeks**: Indeks komposit pada `orders (store_id, created_at)`.

### 7. Produk Terlaris (Horizontal Bar Chart)
* **Deskripsi**: 5 Produk dengan jumlah kuantitas unit terjual paling banyak.
* **SQL Query**:
  ```sql
  SELECT oi.product_id, SUM(oi.quantity) as total_qty 
  FROM order_items oi
  JOIN orders o ON oi.order_id = o.id
  WHERE o.store_id = ? AND o.status = 'completed'
  GROUP BY oi.product_id
  ORDER BY total_qty DESC LIMIT 5;
  ```
* **Optimasi Indeks**: Menghubungkan tabel detail item dengan status order induk.
* **Rekomendasi Indeks**: Indeks komposit pada `order_items (product_id, quantity)`.

### 8. Performa Penjual (Vertical Bar Chart - Admin Overview)
* **Deskripsi**: Perbandingan total omzet bersih antar toko.
* **SQL Query**:
  ```sql
  SELECT s.id, s.name, SUM(o.final_amount) as total_sales 
  FROM stores s
  LEFT JOIN orders o ON s.id = o.store_id AND o.status = 'completed'
  GROUP BY s.id, s.name;
  ```
* **Rekomendasi Indeks**: Indeks komposit pada `orders (store_id, status, final_amount)`.

### 9. Bagan Status Pesanan (Funnel Flow)
* **Deskripsi**: Hitung jumlah pesanan di setiap status transaksi.
* **SQL Query**:
  ```sql
  SELECT status, COUNT(id) as count FROM orders WHERE store_id = ? GROUP BY status;
  ```
* **Rekomendasi Indeks**: Indeks komposit pada `orders (store_id, status)`.

### 10. Dashboard Ringkasan KPI Real-Time
* **Deskripsi**: Informasi total omzet, total order, total produk aktif.
* **SQL Query**:
  ```sql
  SELECT SUM(final_amount) FROM orders WHERE store_id = ? AND status = 'completed';
  SELECT COUNT(id) FROM orders WHERE store_id = ?;
  ```
* **Rekomendasi Indeks**: Indeks komposit pada `orders (store_id, status)`.

### 11. Analisis Rating & Ulasan (Bar Chart)
* **Deskripsi**: Sebaran ulasan bintang 1 hingga bintang 5 yang diperoleh toko.
* **SQL Query**:
  ```sql
  SELECT rating, COUNT(id) as count 
  FROM reviews 
  WHERE product_id IN (SELECT id FROM products WHERE store_id = ?) AND is_moderated = false 
  GROUP BY rating;
  ```
* **Rekomendasi Indeks**: Indeks komposit pada `reviews (product_id, rating, is_moderated)`.

---

## 2. Struktur Tabel Summary `daily_sales_summaries`
Tabel ini digunakan untuk pra-agregasi data transaksi harian, sehingga grafik Tren Penjualan dan Laporan Rekap tidak perlu melakukan kueri agregasi `SUM/COUNT` pada tabel `orders` yang besar.

* **Skema Kolom**:
  * `id` (PK, Auto-increment)
  * `store_id` (FK ke stores)
  * `date` (DATE)
  * `total_sales` (DECIMAL 15,2) - Omzet bersih harian
  * `order_count` (INTEGER) - Jumlah transaksi sukses
  * `created_at` (TIMESTAMP)
  * `updated_at` (TIMESTAMP)
* **Indeks**: Unique Composite Index `(store_id, date)`.

---

## 3. Rencana Tambahan Indeks
Untuk mempercepat pencarian data pelaporan, kita menambahkan indeks komposit berikut:
1. `idx_orders_store_status_date` pada tabel `orders` untuk kolom `(store_id, status, created_at)`.
2. `idx_products_store_deleted` pada tabel `products` untuk kolom `(store_id, deleted_at)`.
3. `idx_order_items_product_qty` pada tabel `order_items` untuk kolom `(product_id, quantity)`.
4. `idx_reviews_product_rating` pada tabel `reviews` untuk kolom `(product_id, rating, is_moderated)`.
