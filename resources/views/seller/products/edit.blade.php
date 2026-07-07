@extends('layouts.app')

@section('title', 'Edit Produk')

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
            <h1 style="font-size: 2rem;">Edit Produk</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Perbarui rincian produk, stok dasar, persentase diskon, dan edit variasi barang.</p>
        </div>

        <div class="glass-card" style="max-width: 900px; hover: none;">
            <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-3" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="price" class="form-label">Harga Base (Rp)</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="discount_percent" class="form-label">Diskon Promo (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent" class="form-control" min="0" max="100" step="0.1" value="{{ old('discount_percent', $product->discount_percent) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="stock" class="form-label">Stok Utama</label>
                        <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi Produk</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                <div style="display: flex; gap: 2rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    @if($product->image)
                        <div style="width: 100px; height: 100px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset($product->image) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endif
                    <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                        <label for="image" class="form-label">Ganti Foto Produk (Max 2MB)</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- Product Variants Block -->
                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.15rem;"><i data-lucide="layers" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Varian Produk</h3>
                        <button type="button" class="btn btn-secondary btn-sm" id="add-variant-btn"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Tambah Baris Varian</button>
                    </div>

                    <div id="variants-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($product->variants as $variant)
                            <div style="display: flex; gap: 1rem; align-items: center;" class="variant-row">
                                <input type="hidden" name="variant_id[]" value="{{ $variant->id }}">
                                
                                <div style="flex: 2;">
                                    <input type="text" name="variant_name[]" class="form-control" value="{{ $variant->name }}" placeholder="Nama Varian" required>
                                </div>
                                <div style="flex: 1.2;">
                                    <input type="number" name="variant_price[]" class="form-control" value="{{ $variant->additional_price }}" placeholder="+ Harga" required>
                                </div>
                                <div style="flex: 1;">
                                    <input type="number" name="variant_stock[]" class="form-control" value="{{ $variant->stock }}" placeholder="Stok" required>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete-row-btn" style="padding: 0.6rem 0.8rem;"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Perbarui Produk</button>
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

        // Hook existing delete buttons
        document.querySelectorAll('.delete-row-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.variant-row').remove();
            });
        });

        function addVariantRow() {
            const row = document.createElement('div');
            row.style.display = 'flex';
            row.style.gap = '1rem';
            row.style.alignItems = 'center';
            row.className = 'variant-row';

            row.innerHTML = `
                <input type="hidden" name="variant_id[]" value="">
                <div style="flex: 2;">
                    <input type="text" name="variant_name[]" class="form-control" placeholder="Nama Varian" required>
                </div>
                <div style="flex: 1.2;">
                    <input type="number" name="variant_price[]" class="form-control" placeholder="+ Harga" value="0" required>
                </div>
                <div style="flex: 1;">
                    <input type="number" name="variant_stock[]" class="form-control" placeholder="Stok" value="0" required>
                </div>
                <div>
                    <button type="button" class="btn btn-danger btn-sm delete-row-btn" style="padding: 0.6rem 0.8rem;"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                </div>
            `;

            container.appendChild(row);
            lucide.createIcons();

            // Hook delete action
            row.querySelector('.delete-row-btn').addEventListener('click', () => {
                row.remove();
            });
        }

        addBtn.addEventListener('click', addVariantRow);
    });
</script>
@endsection
