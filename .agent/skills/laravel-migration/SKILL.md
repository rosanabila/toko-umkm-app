---
name: laravel-migration-helper
description: Panduan pembuatan migration Laravel 10 yang konsisten dengan ERD DBML, mencakup tipe data MySQL, foreign keys, indeks, dan soft deletes.
---

# Panduan Pembuatan Migration Laravel 10

Skill ini memandu agen pengembang dalam menyusun file migration Laravel 10 yang konsisten dengan desain ERD di berkas `docs/database/erd.dbml`.

---

## 1. Urutan Pembuatan Tabel (Migration Ordering)
Agar tidak terjadi error pada database engine saat menjalankan `php artisan migrate` akibat kegagalan referensi kunci asing (*foreign key constraint fails*), migration harus dibuat dengan urutan sebagai berikut:
1. **Tabel Master Independen**: Tabel yang tidak memiliki foreign key ke tabel lain (contoh: `users`, `categories`).
2. **Tabel Dependent**: Tabel yang mereferensikan tabel master (contoh: `stores` yang mereferensikan `users`, `products` yang mereferensikan `stores`).
3. **Tabel Transaksional**: Tabel transaksi utama (contoh: `orders`, `payments`, `order_returns`).
4. **Tabel Pivot / Relasi Many-to-Many**: Tabel jembatan yang direferensikan oleh dua tabel dependent (contoh: `category_product`, `carts`, `order_items`).

---

## 2. Pemetaan Tipe Data (DBML ke Laravel Blueprint)
Gunakan tipe data Laravel Blueprint yang sesuai untuk MySQL:

| Tipe Data DBML | Metode Laravel Blueprint | Tipe Data MySQL |
|---|---|---|
| `integer` | `$table->id()` / `$table->foreignId()` | `BIGINT UNSIGNED` (Auto-Increment / FK) |
| `varchar` | `$table->string('col', length)` | `VARCHAR(255)` (default) |
| `text` | `$table->text('col')` | `TEXT` |
| `decimal` | `$table->decimal('col', 15, 2)` | `DECIMAL(15,2)` (untuk keuangan/harga) |
| `boolean` | `$table->boolean('col')` | `TINYINT(1)` |
| `date` | `$table->date('col')` | `DATE` |
| `timestamp` | `$table->timestamp('col')` | `TIMESTAMP` |
| `datetime` | `$table->dateTime('col')` | `DATETIME` |

---

## 3. Best Practices (Indexes, Keys, Soft Deletes, Timestamps)
- **Soft Deletes**: Selalu tambahkan `$table->softDeletes();` pada tabel master yang memiliki relasi transaksional (seperti `products`, `vouchers`, `users`, `stores`) agar penghapusan data tidak merusak histori transaksi.
- **Timestamps**: Selalu gunakan `$table->timestamps();` untuk otomatis membuat kolom `created_at` dan `updated_at`.
- **Indeks Kunci Asing**: Gunakan `$table->foreignId('user_id')->constrained()->onDelete('cascade');` untuk mendefinisikan foreign key dengan onDelete cascade otomatis.

---

## 4. Pola Implementasi Kode Migration

### A. Pola Tabel Transaksional (Contoh: `orders`)
Tabel transaksi membutuhkan penomoran unik, relasi foreign key dengan aksi restrict (mencegah penghapusan induk saat transaksi ada), dan penyimpanan alamat historis.
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('buyer_id')->constrained('users')->onDelete('restrict');
    $table->foreignId('store_id')->constrained('stores')->onDelete('restrict');
    $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
    
    $table->string('order_number')->unique();
    $table->decimal('total_price', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('shipping_cost', 15, 2);
    $table->decimal('grand_total', 15, 2);
    
    $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled', 'returned'])->default('pending');
    $table->text('shipping_address');
    $table->string('shipping_courier');
    
    $table->timestamps();
});
```

### B. Pola Tabel Pivot (Contoh: `category_product`)
Tabel pivot many-to-many membutuhkan composite unique index untuk mencegah duplikasi pemetaan relasi yang sama, serta cascade delete.
```php
Schema::create('category_product', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
    $table->timestamps();

    // Composite unique index
    $table->unique(['product_id', 'category_id']);
});
```

### C. Pola Tabel dengan Indeks Komposit (Contoh: `carts`)
Gunakan composite index pada tabel pencarian cepat untuk mengoptimalkan kinerja query database ketika memuat data keranjang per user.
```php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
    $table->integer('quantity');
    $table->timestamps();

    // Composite index untuk mempercepat query select cart per user & barang
    $table->index(['user_id', 'product_id', 'product_variant_id'], 'user_cart_search_index');
});
```
