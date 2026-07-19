---
name: laravel-model
description: Panduan konvensi pembuatan dan konfigurasi Model Eloquent pada proyek TokoKita (Laravel 10).
---

# Konvensi Penulisan Model Eloquent - TokoKita

Panduan ini mendokumentasikan aturan dan standar dalam pembuatan serta modifikasi model Eloquent ORM di proyek **TokoKita** untuk menjamin konsistensi, keamanan, dan performa query database.

---

## 1. Struktur Dasar Model (Boilerplate)
Setiap model Eloquent harus diletakkan di direktori `app/Models/` menggunakan namespace `App\Models` dan menerapkan trait `HasFactory`.

*   **Definisi Nama Tabel**: Nyatakan nama tabel secara eksplisit menggunakan properti `protected $table` jika tabel database menggunakan bentuk jamak kustom (misal: `daily_sales_summaries`).
*   **Keamanan Mass Assignment**: Gunakan whitelist `$fillable` untuk menentukan kolom mana saja yang boleh diisi secara massal (mass-assigned). Hindari penggunaan `$guarded`.

*Contoh:*
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesSummary extends Model
{
    use HasFactory;

    protected $table = 'daily_sales_summaries';

    protected $fillable = [
        'store_id',
        'date',
        'total_sales',
        'order_count',
    ];
}
```

---

## 2. Type Casting ($casts)
Gunakan properti `$casts` untuk mengubah tipe data kolom database secara otomatis ke tipe objek/primitif yang sesuai saat dibaca dari database:
*   `date` / `datetime`: Untuk kolom tanggal/waktu.
*   `decimal:2` / `float`: Untuk data keuangan/nominal.
*   `integer` / `boolean`: Untuk data angka bulat atau status biner.
*   `array` / `json`: Untuk menyimpan data semi-terstruktur (seperti `shipping_areas` pada tabel `stores`).
*   `hashed`: Untuk kolom kata sandi pengguna (`password` pada model `User`).

```php
protected $casts = [
    'date' => 'date',
    'total_sales' => 'decimal:2',
    'order_count' => 'integer',
];
```

---

## 3. Hubungan Relasi (Eloquent Relationships)
Hubungan antar tabel didefinisikan secara eksplisit dengan menyertakan nama kolom kunci asing (*foreign key*) secara manual demi kejelasan pembacaan skema relasi.

### Konvensi Penamaan Metode Relasi:
*   **Singular (Tunggal)**: Untuk relasi ber-kardinalitas `1` (menggunakan `belongsTo` atau `hasOne`). Contoh: `user()`, `store()`, `order()`.
*   **Plural (Jamak)**: Untuk relasi ber-kardinalitas `N` atau Banyak (menggunakan `hasMany` atau `belongsToMany`). Contoh: `products()`, `items()`, `wishlistProducts()`.

### Contoh Penulisan Relasi:

```php
// Relasi 1-to-Many (Inverse) pada Model DailySalesSummary
public function store()
{
    return $this->belongsTo(Store::class, 'store_id');
}

// Relasi Many-to-Many pada Model User (Menghubungkan via tabel Wishlist)
public function wishlistProducts()
{
    return $this->belongsToMany(Product::class, 'wishlists', 'user_id', 'product_id')->withTimestamps();
}
```

---

## 4. Accessors & Mutators (Dynamic Attributes)
Untuk membuat kolom virtual yang dihitung secara dinamis, gunakan konvensi Accessor klasik Laravel 10:

*   **Format**: `get[NamaKolomCamelCase]Attribute`
*   **Panggilan**: Diakses seperti properti biasa (camel_case/snake_case) tanpa tanda kurung.

*Contoh Accessor Subtotal Keranjang Belanja:*
```php
public function getSubtotalAttribute()
{
    $basePrice = $this->product->discounted_price;
    $variantPrice = $this->variant ? $this->variant->additional_price : 0.00;
    return ($basePrice + $variantPrice) * $this->quantity;
}
```
*Pemanggilan*: `$cartItem->subtotal`

---

## 5. Metode Pembantu Logika Bisnis (Helper Methods)
Model pada TokoKita juga dapat bertindak sebagai tempat logika bisnis mandiri (*Rich Domain Model*) untuk memperkecil penulisan kode di Controller:
*   Memeriksa peran user: `isAdmin()`, `isPenjual()`, `isPembeli()`.
*   Memvalidasi voucher: `isValidFor($amount)`.
*   Menghitung potongan diskon: `calculateDiscount($amount)`.

```php
public function isValidFor($amount): bool
{
    $today = date('Y-m-d');
    return $this->active 
        && $this->start_date <= $today 
        && $this->end_date >= $today 
        && $amount >= $this->min_spend;
}
```
