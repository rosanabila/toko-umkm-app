# Workflow Otomatisasi Pembuatan Modul CRUD (TokoKita)

Workflow ini memberikan panduan langkah-demi-langkah bagi agen atau pengembang untuk memproduksi modul CRUD (Create, Read, Update, Delete) secara konsisten dan lengkap berdasarkan satu nama entitas sebagai masukan.

---

## 1. Input Entitas
Tentukan nama entitas dalam format **PascalCase** tunggal (misal: `Product`, `Voucher`, `Wishlist`, `Category`).
*   **Nama Entitas (Singular)**: `{Entity}` (contoh: `Wishlist`)
*   **Nama Entitas (Plural/Jamak)**: `{EntityPlural}` (contoh: `wishlists`)
*   **Akses Role**: Tentukan role yang berhak mengakses modul ini (`admin`, `penjual`, `pembeli`).

---

## 2. Langkah 1: Pembuatan Database Migration
Buat berkas migrasi baru menggunakan Artisan CLI:
```bash
php artisan make:migration create_{entity_plural}_table
```
Di dalam file migrasi, terapkan aturan berikut:
*   Gunakan tipe data MySQL yang presisi (misal: `decimal(15, 2)` untuk uang/harga, `integer` untuk stok).
*   Definisikan *foreign key* secara eksplisit dengan `.constrained()->onDelete('cascade' | 'restrict')`.
*   Tambahkan indeks komposit pada kolom-kolom yang sering disaring (*filter*) atau diurutkan (*sort*) untuk optimasi performa.

---

## 3. Langkah 2: Pembuatan Eloquent Model
Buat berkas model di `app/Models/{Entity}.php` mengikuti konvensi **laravel-model**:
*   Terapkan trait `use HasFactory;`.
*   Tulis properti `protected $table = '{entity_plural}';` secara eksplisit.
*   Tulis properti `protected $fillable = [...]` dengan daftar kolom yang aman diisi massal.
*   Tulis properti `protected $casts = [...]` untuk konversi tipe data otomatis (tanggal, biner, desimal).
*   Definisikan metode relasi dengan nama **tunggal** untuk kardinalitas 1 (`belongsTo`, `hasOne`) dan **jamak** untuk kardinalitas banyak (`hasMany`, `belongsToMany`). Cantumkan kunci asing (*foreign key*) secara eksplisit.

---

## 4. Langkah 3: Pembuatan Form Request
Buat dua berkas Form Request di `app/Http/Requests/`:
1.  **`Store{Entity}Request.php`**
2.  **`Update{Entity}Request.php`**

Metode di dalam kelas request:
*   `authorize()`: Validasi bahwa pengguna sedang login dan memiliki role akses yang sesuai.
*   `rules()`: Aturan validasi lengkap. Gunakan aturan `unique` yang aman saat proses pembaruan data dengan mengecualikan ID entitas saat ini:
    ```php
    'code' => 'required|string|unique:vouchers,code,' . $this->route('{entity_snake_case}')
    ```

---

## 5. Langkah 4: Pembuatan Resource Controller & Service Class (Service Layer)

### A. Kapan Menggunakan Service Class?
*   Jika operasi CRUD melibatkan logika bisnis kompleks (misalnya: kalkulasi keuangan, manipulasi data multi-tabel, integrasi eksternal, atau pemrosesan stok), **jangan** letakkan logika tersebut di Controller.
*   Buat berkas layanan baru di `app/Services/{Entity}Service.php` untuk menampung logika tersebut.

### B. Membuat Service Class `app/Services/{Entity}Service.php` (Opsional tapi Direkomendasikan untuk Logika Kompleks)
*   Bungkus seluruh query penulisan database transaksional ke dalam penanganan **Database Transaction** (`DB::transaction(function() { ... })`).
*   Lempar eksepsi `\Exception` jika terjadi kesalahan validasi bisnis di dalam service.

### C. Pembuatan Controller `app/Http/Controllers/{Entity}Controller.php`
*   Gunakan dependency injection dari `Store{Entity}Request` untuk metode `store` dan `Update{Entity}Request` untuk metode `update`.
*   Injeksikan `{Entity}Service` melalui **Constructor Dependency Injection** jika menggunakan Service Layer.
*   Tangkap eksepsi dari Service Class menggunakan blok `try-catch` di Controller, lalu kembalikan pengalihan halaman dengan pesan flash sukses atau kesalahan:
    ```php
    public function store(Store{Entity}Request $request)
    {
        try {
            $this->{entity}Service->create($request->all());
            return redirect()->route('{role}.{entity_plural}.index')->with('success', '{Entity} berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }
    ```

---

## 6. Langkah 5: Pendaftaran Rute (`routes/web.php`)
Daftarkan rute resource di berkas `routes/web.php` dalam grup middleware otentikasi dan role yang sesuai:
```php
Route::middleware(['auth', 'role:{role}'])->group(function () {
    Route::resource('/{entity_plural}', {Entity}Controller::class);
});
```

---

## 7. Langkah 6: Pembuatan Berkas Views (Tampilan)
Buat folder views di `resources/views/{role}/{entity_plural}/` berisi berkas Blade berikut:

*   **`index.blade.php`**:
    *   Tabel atau grid responsif dibungkus kelas `.glass-card`.
    *   Menampilkan data dengan paginasi (`{{ $items->links() }}`).
    *   Tombol Tambah Baru, tombol Ubah, dan tombol Hapus (menggunakan form POST dengan `@method('DELETE')` dan konfirmasi javascript).
*   **`create.blade.php`**:
    *   Form penginputan data dengan `@csrf`.
    *   Tampilkan pesan eror validasi per kolom menggunakan `@error('field')` atau ringkasan `@if($errors->any())`.
    *   Input didesain menggunakan kelas `.form-control` dan `.form-group`.
*   **`edit.blade.php`**:
    *   Sama seperti form tambah, namun field terpopulasi dengan data lama (`value="{{ old('field', $item->field) }}"`).
    *   Menyertakan tag `@method('PUT')` atau `@method('PATCH')`.
*   **`show.blade.php`**:
    *   Kartu visual detail menampilkan properti lengkap dari satu record data.

Terapkan style **Tailwind CSS** yang konsisten dengan tema **TokoKita** (penggunaan gradien, warna-warna HSL, radius kelengkungan rounded-xl/rounded-md, dan layout responsif). Pastikan ikon diinisialisasi ulang di bagian bawah file layout master dengan Lucide script.
