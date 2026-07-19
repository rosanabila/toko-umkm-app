@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">
            <i data-lucide="store" style="color: var(--primary);"></i>
            <span>{{ $store->name }}</span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('seller.dashboard') }}" class="sidebar-link">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('seller.profile') }}" class="sidebar-link">
                    <i data-lucide="settings"></i> Profil Toko
                </a>
            </li>
            <li>
                <a href="{{ route('seller.products.index') }}" class="sidebar-link active">
                    <i data-lucide="package"></i> Produk Toko
                </a>
            </li>
            <li>
                <a href="{{ route('seller.vouchers.index') }}" class="sidebar-link">
                    <i data-lucide="ticket"></i> Voucher Belanja
                </a>
            </li>
            <li>
                <a href="{{ route('seller.orders') }}" class="sidebar-link">
                    <i data-lucide="shopping-cart"></i> Pesanan Masuk
                </a>
            </li>
            <li>
                <a href="{{ route('seller.reports') }}" class="sidebar-link">
                    <i data-lucide="bar-chart-2"></i> Laporan Analitis
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Tambah Produk Baru</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Masukkan detail produk beserta varian opsional yang ingin Anda jual.</p>
        </div>

        <div class="glass-card" style="max-width: 900px; hover: none;">
            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($errors->any())
                    <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Kemeja Flanel Slimfit" value="{{ old('name') }}" required>
                        <div id="name-error" style="color: var(--status-cancelled); font-size: 0.8rem; margin-top: 0.25rem; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-3" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="price" class="form-label">Harga Base (Rp)</label>
                        <input type="number" name="price" id="price" class="form-control" placeholder="Contoh: 100000" value="{{ old('price') }}" required>
                        <div id="price-error" style="color: var(--status-cancelled); font-size: 0.8rem; margin-top: 0.25rem; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="discount_percent" class="form-label">Diskon Promo (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent" class="form-control" placeholder="0" min="0" max="100" step="0.1" value="{{ old('discount_percent', 0) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="stock" class="form-label">Stok Utama</label>
                        <input type="number" name="stock" id="stock" class="form-control" placeholder="Contoh: 50" value="{{ old('stock', 0) }}" required>
                        <div id="stock-error" style="color: var(--status-cancelled); font-size: 0.8rem; margin-top: 0.25rem; display: none;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi Produk</label>
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Tuliskan spesifikasi produk, keunggulan, bahan, detail ukuran, dll.">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Foto Produk (Max 2MB)</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <!-- Product Variants Block -->
                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.15rem;"><i data-lucide="layers" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Varian Produk (Opsional)</h3>
                        <button type="button" class="btn btn-secondary btn-sm" id="add-variant-btn"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Tambah Baris Varian</button>
                    </div>

                    <div id="variants-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <!-- JS Appends rows here -->
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Produk Baru</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('variants-container');
        const addBtn = document.getElementById('add-variant-btn');

        function addVariantRow() {
            const row = document.createElement('div');
            row.style.display = 'flex';
            row.style.gap = '1rem';
            row.style.alignItems = 'center';
            row.className = 'variant-row';

            row.innerHTML = `
                <div style="flex: 2;">
                    <input type="text" name="variant_name[]" class="form-control" placeholder="Nama Varian (misal: Merah, XL)" required>
                </div>
                <div style="flex: 1.2;">
                    <input type="number" name="variant_price[]" class="form-control" placeholder="+ Harga (Rp)" value="0" required>
                </div>
                <div style="flex: 1;">
                    <input type="number" name="variant_stock[]" class="form-control" placeholder="Stok Varian" value="0" required>
                </div>
                <div>
                    <button type="button" class="btn btn-danger btn-sm delete-row-btn" style="padding: 0.6rem 0.8rem;"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                </div>
            `;

            container.appendChild(row);
            lucide.createIcons();

            // Hook delete action with confirmation
            row.querySelector('.delete-row-btn').addEventListener('click', () => {
                if (confirm('Apakah Anda yakin ingin menghapus baris varian ini?')) {
                    row.remove();
                }
            });
        }

        addBtn.addEventListener('click', addVariantRow);

        // Real-time validation
        const nameInput = document.getElementById('name');
        const priceInput = document.getElementById('price');
        const stockInput = document.getElementById('stock');
        const form = document.querySelector('form');

        const nameError = document.getElementById('name-error');
        const priceError = document.getElementById('price-error');
        const stockError = document.getElementById('stock-error');

        function validateName() {
            const val = nameInput.value.trim();
            if (val.length < 5) {
                nameError.innerText = 'Nama produk minimal harus terdiri dari 5 karakter.';
                nameError.style.display = 'block';
                return false;
            } else {
                nameError.style.display = 'none';
                return true;
            }
        }

        function validatePrice() {
            const val = parseFloat(priceInput.value);
            if (isNaN(val) || val < 0) {
                priceError.innerText = 'Harga base tidak boleh bernilai negatif.';
                priceError.style.display = 'block';
                return false;
            } else {
                priceError.style.display = 'none';
                return true;
            }
        }

        function validateStock() {
            const val = parseFloat(stockInput.value);
            if (isNaN(val) || val < 0 || !Number.isInteger(val)) {
                stockError.innerText = 'Stok utama harus berupa angka bulat dan tidak boleh negatif.';
                stockError.style.display = 'block';
                return false;
            } else {
                stockError.style.display = 'none';
                return true;
            }
        }

        nameInput.addEventListener('input', validateName);
        priceInput.addEventListener('input', validatePrice);
        stockInput.addEventListener('input', validateStock);

        form.addEventListener('submit', (e) => {
            const isNameValid = validateName();
            const isPriceValid = validatePrice();
            const isStockValid = validateStock();

            if (!isNameValid || !isPriceValid || !isStockValid) {
                e.preventDefault();
                alert('Silakan perbaiki kesalahan validasi sebelum menyimpan produk.');
            }
        });
    });
</script>
@endsection
