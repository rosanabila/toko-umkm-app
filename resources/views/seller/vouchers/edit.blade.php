@extends('layouts.app')

@section('title', 'Edit Voucher')

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
                <a href="{{ route('seller.products.index') }}" class="sidebar-link">
                    <i data-lucide="package"></i> Produk Toko
                </a>
            </li>
            <li>
                <a href="{{ route('seller.vouchers.index') }}" class="sidebar-link active">
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
            <h1 style="font-size: 2rem;">Edit Voucher Belanja</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Ubah rincian, nilai potongan, syarat minimal belanja, atau masa berlaku voucher promo Anda.</p>
        </div>

        <div class="glass-card" style="max-width: 700px; hover: none;">
            <form action="{{ route('seller.vouchers.update', $voucher->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <label for="code" class="form-label">Kode Voucher (Unik, Huruf Kapital)</label>
                    <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $voucher->code) }}" style="text-transform: uppercase;" required>
                </div>

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="type" class="form-label">Tipe Potongan Diskon</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Potongan Harga Flat (Rp)</option>
                            <option value="percent" {{ old('type', $voucher->type) === 'percent' ? 'selected' : '' }}>Persentase Diskon (%)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="value" class="form-label">Nilai Diskon</label>
                        <input type="number" name="value" id="value" class="form-control" value="{{ old('value', $voucher->value) }}" required>
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="min_spend" class="form-label">Minimal Belanja Syarat (Rp)</label>
                        <input type="number" name="min_spend" id="min_spend" class="form-control" value="{{ old('min_spend', $voucher->min_spend) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="active" class="form-label">Status Aktif Ketersediaan</label>
                        <select name="active" id="active" class="form-control" required>
                            <option value="1" {{ old('active', $voucher->active) == 1 ? 'selected' : '' }}>Aktif (Tersedia digunakan)</option>
                            <option value="0" {{ old('active', $voucher->active) == 0 ? 'selected' : '' }}>Nonaktif (Ditangguhkan)</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="start_date" class="form-label">Tanggal Mulai Berlaku</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $voucher->start_date) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date" class="form-label">Tanggal Berakhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $voucher->end_date) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Simpan Perubahan Voucher</button>
            </form>
        </div>
    </div>
</div>
@endsection
