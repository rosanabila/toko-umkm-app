@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">
            <i data-lucide="shield-check" style="color: var(--primary);"></i>
            <span>Admin Panel</span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link active">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="sidebar-link">
                    <i data-lucide="users"></i> Pengguna & Role
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews') }}" class="sidebar-link">
                    <i data-lucide="message-square"></i> Moderasi Ulasan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.returns') }}" class="sidebar-link">
                    <i data-lucide="refresh-cw"></i> Retur Barang
                    @php
                        $pendingReturns = \App\Models\OrderReturn::where('status', 'pending')->count();
                    @endphp
                    @if($pendingReturns > 0)
                        <span style="background: var(--status-returned); color: white; font-size: 10px; padding: 0.1rem 0.4rem; border-radius: 999px; margin-left: auto;">{{ $pendingReturns }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Panel Sistem Administrator</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Pantau perputaran omzet transaksi, kelola hak akses role pengguna, moderasi ulasan produk, dan keluhan retur.</p>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                <span class="label">Total Omzet Sistem</span>
                <span class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <span class="label">Total Pengguna</span>
                <span class="value">{{ $totalUsers }} User</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #581c87 0%, #6b21a8 100%);">
                <span class="label">Toko UMKM Aktif</span>
                <span class="value">{{ $totalStores }} Toko</span>
            </div>
            
            <div class="kpi-card" style="background: linear-gradient(135deg, #034f84 0%, #0369a1 100%);">
                <span class="label">Total Transaksi</span>
                <span class="value">{{ $totalOrders }} Order</span>
            </div>
        </div>

        <!-- Graphical Stats -->
        <div class="grid-2">
            <!-- 1. Performa Penjual -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="bar-chart-3" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Grafik Perbandingan Omzet Toko (IDR)</h3>
                <div style="flex: 1; position: relative;">
                    <canvas id="sellerPerformanceChart"></canvas>
                </div>
            </div>

            <!-- 2. Bagan Status Pesanan -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="pie-chart" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Bagan Status Alur Pesanan (Global)</h3>
                <div style="flex: 1; position: relative; max-height: 250px; display: flex; justify-content: center;">
                    <canvas id="globalOrderFlowChart" style="max-width: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sellerPerfRaw = @json($sellerPerformance);
        const orderFlowRaw = @json($orderFlow);

        // 1. Bar Chart: Seller Performance comparison
        const sellerLabels = sellerPerfRaw.map(item => item.name);
        const sellerSales = sellerPerfRaw.map(item => parseFloat(item.total_sales || 0));

        new Chart(document.getElementById('sellerPerformanceChart'), {
            type: 'bar',
            data: {
                labels: sellerLabels.length > 0 ? sellerLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Omzet Bersih (Rp)',
                    data: sellerSales.length > 0 ? sellerSales : [0],
                    backgroundColor: '#825af6',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });

        // 2. Doughnut Chart: Order Flow Statuses
        const flowLabels = orderFlowRaw.map(item => item.status.toUpperCase());
        const flowCounts = orderFlowRaw.map(item => parseInt(item.count));

        const flowColors = {
            'PENDING': '#eab308',
            'PROCESSING': '#3b82f6',
            'SHIPPED': '#6366f1',
            'COMPLETED': '#10b981',
            'CANCELLED': '#ef4444',
            'RETURNED': '#f97316'
        };
        const bgColors = flowLabels.map(label => flowColors[label] || '#cbd5e1');

        new Chart(document.getElementById('globalOrderFlowChart'), {
            type: 'doughnut',
            data: {
                labels: flowLabels.length > 0 ? flowLabels : ['Belum ada order'],
                datasets: [{
                    data: flowCounts.length > 0 ? flowCounts : [1],
                    backgroundColor: flowCounts.length > 0 ? bgColors : ['#cbd5e1'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endsection
