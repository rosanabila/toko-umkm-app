@extends('layouts.app')

@section('title', 'Voucher Toko Saya')

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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem;">Voucher Belanja</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Buat kupon promo diskon untuk meningkatkan transaksi penjualan produk toko Anda.</p>
            </div>
            
            <a href="{{ route('seller.vouchers.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> Tambah Voucher</a>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($vouchers->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 4rem 2rem;">Belum ada voucher belanja di toko Anda. Klik "Tambah Voucher" untuk membuat kupon promo!</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Voucher</th>
                                <th>Tipe Potongan</th>
                                <th style="text-align: right;">Nilai Diskon</th>
                                <th style="text-align: right;">Min. Belanja</th>
                                <th style="text-align: center;">Masa Berlaku</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                            <tr>
                                <td><span style="font-family: monospace; font-size: 1.1rem; font-weight: bold; background: #e0e7ff; color: #4f46e5; padding: 0.25rem 0.75rem; border-radius: 4px; border: 1px dashed #6366f1;">{{ $voucher->code }}</span></td>
                                <td>{{ $voucher->type === 'percent' ? 'Persentase (%)' : 'Potongan Tetap (Rp)' }}</td>
                                <td style="text-align: right; font-weight: 600;">
                                    {{ $voucher->type === 'percent' ? number_format($voucher->value, 0) . '%' : 'Rp ' . number_format($voucher->value, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    Rp {{ number_format($voucher->min_spend, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                                    {{ date('d M Y', strtotime($voucher->start_date)) }} - {{ date('d M Y', strtotime($voucher->end_date)) }}
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $today = date('Y-m-d');
                                        $isExpired = $voucher->end_date < $today;
                                    @endphp
                                    @if($isExpired)
                                        <span class="badge badge-cancelled">Kedaluwarsa</span>
                                    @elseif($voucher->active)
                                        <span class="badge badge-completed">Aktif</span>
                                    @else
                                        <span class="badge badge-pending">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('seller.vouchers.edit', $voucher->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem;"><i data-lucide="edit-2" style="width: 14px; height: 14px;"></i></a>
                                    <form action="{{ route('seller.vouchers.destroy', $voucher->id) }}" method="POST" style="display: inline;" onsubmit="return confirmAction(event, 'Hapus voucher belanja ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
