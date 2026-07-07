@extends('layouts.app')

@section('title', 'Manajemen Pesanan Masuk')

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
                <a href="{{ route('seller.vouchers.index') }}" class="sidebar-link">
                    <i data-lucide="ticket"></i> Voucher Belanja
                </a>
            </li>
            <li>
                <a href="{{ route('seller.orders') }}" class="sidebar-link active">
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
            <h1 style="font-size: 2rem;">Manajemen Pesanan Masuk</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Proses pembayaran dan pesanan masuk dari pembeli di toko UMKM Anda.</p>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($orders->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 3rem 2rem;">Belum ada pesanan belanja masuk ke toko Anda.</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Nama Pembeli</th>
                                <th>Kontak Penerima</th>
                                <th>Tanggal Masuk</th>
                                <th style="text-align: right;">Total Bayar</th>
                                <th style="text-align: center;">Status Pesanan</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->buyer->name }}</td>
                                <td>{{ $order->shipping_recipient_name }} ({{ $order->shipping_recipient_phone }})</td>
                                <td style="color: var(--text-muted);">{{ $order->created_at->format('d M Y H:i') }} WIB</td>
                                <td style="text-align: right; font-weight: 600;">
                                    Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $order->status }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('seller.orderDetail', $order->id) }}" class="btn btn-secondary btn-sm">Kelola</a>
                                    <a href="{{ route('seller.deliveryNotePdf', $order->id) }}" target="_blank" class="btn btn-primary btn-sm" style="background-color: var(--primary); box-shadow: none;"><i data-lucide="truck" style="width: 12px; height: 12px;"></i> Surat Jalan</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
