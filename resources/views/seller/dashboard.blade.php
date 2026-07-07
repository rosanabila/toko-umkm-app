@extends('layouts.app')

@section('title', 'Dashboard Penjual')

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
                <a href="{{ route('seller.dashboard') }}" class="sidebar-link active">
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
                <a href="{{ route('seller.orders') }}" class="sidebar-link">
                    <i data-lucide="shopping-cart"></i> Pesanan Masuk
                    @php
                        $pendingOrders = \App\Models\Order::where('store_id', $store->id)->where('status', 'pending')->count();
                    @endphp
                    @if($pendingOrders > 0)
                        <span style="background: var(--status-pending); color: white; font-size: 10px; padding: 0.1rem 0.4rem; border-radius: 999px; margin-left: auto;">{{ $pendingOrders }}</span>
                    @endif
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2rem;">Dashboard Penjual</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Kelola produk, pesanan, dan pantau performa omzet toko UMKM Anda.</p>
            </div>
            <a href="{{ route('store.show', $store->slug) }}" target="_blank" class="btn btn-secondary btn-sm"><i data-lucide="external-link"></i> Kunjungi Toko</a>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <span class="label">Total Omzet Bersih</span>
                <span class="value">Rp {{ number_format($omzet, 0, ',', '.') }}</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);">
                <span class="label">Pesanan Masuk</span>
                <span class="value">{{ $totalOrders }} order</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                <span class="label">Koleksi Produk</span>
                <span class="value">{{ $totalProducts }} item</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                <span class="label">Butuh Verifikasi Bayar</span>
                <span class="value">{{ $pendingPaymentsCount }} transaksi</span>
            </div>
        </div>

        <!-- Order Funnel Flow Bagan -->
        <div class="glass-card" style="margin-bottom: 2rem; hover: none;">
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;"><i data-lucide="git-commit" style="color: var(--primary); display: inline-block; vertical-align: middle;"></i> Bagan Alur Status Pesanan</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Visualisasi jumlah pesanan berdasarkan tahapan pemrosesan transaksi belanja saat ini.</p>
            
            <div class="status-flow">
                <div class="flow-step {{ $statusCounts['pending'] > 0 ? 'active' : '' }}">
                    <div class="step-count">{{ $statusCounts['pending'] }}</div>
                    <div class="step-label">Menunggu Bayar</div>
                </div>
                <div class="flow-step {{ $statusCounts['processing'] > 0 ? 'active' : '' }}">
                    <div class="step-count">{{ $statusCounts['processing'] }}</div>
                    <div class="step-label">Diproses</div>
                </div>
                <div class="flow-step {{ $statusCounts['shipped'] > 0 ? 'active' : '' }}">
                    <div class="step-count">{{ $statusCounts['shipped'] }}</div>
                    <div class="step-label">Dikirim</div>
                </div>
                <div class="flow-step {{ $statusCounts['completed'] > 0 ? 'active' : '' }}">
                    <div class="step-count">{{ $statusCounts['completed'] }}</div>
                    <div class="step-label">Selesai</div>
                </div>
                <div class="flow-step {{ $statusCounts['returned'] > 0 ? 'active' : '' }}" style="border-color: rgba(249, 115, 22, 0.2);">
                    <div class="step-count" style="color: var(--status-returned);">{{ $statusCounts['returned'] }}</div>
                    <div class="step-label">Retur Komplain</div>
                </div>
                <div class="flow-step {{ $statusCounts['cancelled'] > 0 ? 'active' : '' }}" style="border-color: rgba(239, 68, 68, 0.2);">
                    <div class="step-count" style="color: var(--status-cancelled);">{{ $statusCounts['cancelled'] }}</div>
                    <div class="step-label">Batal</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="clipboard-list" style="color: var(--primary);"></i>
                Pesanan Masuk Terbaru
            </h3>

            @if($recentOrders->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Belum ada pesanan belanja masuk ke toko Anda.</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pembeli</th>
                                <th>Tanggal</th>
                                <th style="text-align: right;">Total Bayar</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->buyer->name }}</td>
                                <td style="color: var(--text-muted);">{{ $order->created_at->format('d M H:i') }} WIB</td>
                                <td style="text-align: right; font-weight: 600;">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $order->status }}">{{ $order->status }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('seller.orderDetail', $order->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem;">Kelola</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
