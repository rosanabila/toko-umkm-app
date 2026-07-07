@extends('layouts.app')

@section('title', 'Profil Toko Saya')

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
                <a href="{{ route('seller.profile') }}" class="sidebar-link active">
                    <i data-lucide="settings"></i> Profil Toko
                </a>
            </li>
            <li>
                <a href="{{ route('seller.products.index') }}" class="sidebar-link">
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
            <h1 style="font-size: 2rem;">Pengaturan Profil Toko UMKM</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Lengkapi informasi publik, jam operasional, dan jangkauan wilayah pengiriman kurir toko Anda.</p>
        </div>

        <div class="glass-card" style="max-width: 800px; hover: none;">
            <form action="{{ route('seller.profileUpdate') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($errors->any())
                    <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-bottom: 1.5rem; align-items: center;">
                    <!-- Current Logo display -->
                    <div style="width: 100px; height: 100px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; overflow: hidden; border: 2px solid var(--border-color);">
                        @if($store->logo)
                            <img src="{{ asset($store->logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ substr($store->name, 0, 1) }}
                        @endif
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                        <label for="logo" class="form-label">Unggah Logo Toko Baru (Max 1MB)</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Toko UMKM</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $store->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Telepon Kontak Toko</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $store->phone) }}" placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi Singkat Toko</label>
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Tuliskan produk utama yang dijual, visi toko Anda, dll.">{{ old('description', $store->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat Fisik Toko</label>
                    <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $store->address) }}" placeholder="Contoh: Jl. Braga No. 10, Bandung">
                </div>

                <div class="grid-3" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="operating_hours_open" class="form-label">Jam Buka Operasional</label>
                        <input type="time" name="operating_hours_open" id="operating_hours_open" class="form-control" value="{{ old('operating_hours_open', substr($store->operating_hours_open, 0, 5)) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="operating_hours_close" class="form-label">Jam Tutup Operasional</label>
                        <input type="time" name="operating_hours_close" id="operating_hours_close" class="form-control" value="{{ old('operating_hours_close', substr($store->operating_hours_close, 0, 5)) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="shipping_areas" class="form-label">Jangkauan Wilayah Kirim</label>
                        @php
                            $areasString = is_array($store->shipping_areas) ? implode(', ', $store->shipping_areas) : '';
                        @endphp
                        <input type="text" name="shipping_areas" id="shipping_areas" class="form-control" value="{{ old('shipping_areas', $areasString) }}" placeholder="Pisahkan dengan koma, cth: Bandung, Jakarta, Cimahi">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%;">Simpan Perubahan Profil</button>
            </form>
        </div>
    </div>
</div>
@endsection
