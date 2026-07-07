@extends('layouts.app')

@section('title', 'Laporan Analitis Toko')

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
                <a href="{{ route('seller.orders') }}" class="sidebar-link">
                    <i data-lucide="shopping-cart"></i> Pesanan Masuk
                </a>
            </li>
            <li>
                <a href="{{ route('seller.reports') }}" class="sidebar-link active">
                    <i data-lucide="bar-chart-2"></i> Laporan Analitis
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content" style="display: flex; flex-direction: column; gap: 2rem;">
        <div>
            <h1 style="font-size: 2rem;">Laporan & Analitis Penjualan</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Unduh dokumen laporan resmi dan visualisasikan statistik pertumbuhan performa toko Anda.</p>
        </div>

        <!-- Export Documents Action Panel -->
        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem;"><i data-lucide="download" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Ekspor Dokumen Laporan</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <a href="{{ route('seller.reports.salesCsv') }}" class="btn btn-secondary" style="flex: 1; min-width: 250px;">
                    <i data-lucide="file-spreadsheet" style="color: #10b981;"></i> Ekspor Rekap Penjualan (Excel/CSV)
                </a>
                <a href="{{ route('seller.reports.ordersCsv') }}" class="btn btn-secondary" style="flex: 1; min-width: 250px;">
                    <i data-lucide="file-text" style="color: #3b82f6;"></i> Ekspor Data Pesanan & Pembeli (Excel/CSV)
                </a>
                <a href="{{ route('seller.reports.stockPdf') }}" target="_blank" class="btn btn-secondary" style="flex: 1; min-width: 250px;">
                    <i data-lucide="file-down" style="color: #ef4444;"></i> Cetak Laporan Stok Produk (PDF)
                </a>
            </div>
        </div>

        <!-- Charts grid -->
        <div class="grid-2">
            <!-- 1. Tren Penjualan per Periode -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="trending-up" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Tren Penjualan Harian (30 Hari Terakhir)</h3>
                <div style="flex: 1; position: relative;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

            <!-- 2. Produk Terlaris -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="award" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> 5 Produk Terlaris (Kuantitas Terjual)</h3>
                <div style="flex: 1; position: relative;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <!-- 3. Bagan Status Pesanan -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="pie-chart" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Sebaran Status Pesanan Belanja</h3>
                <div style="flex: 1; position: relative; max-height: 250px; display: flex; justify-content: center;">
                    <canvas id="orderFlowChart" style="max-width: 250px;"></canvas>
                </div>
            </div>

            <!-- 4. Analisis Rating & Ulasan -->
            <div class="glass-card" style="padding: 1.5rem; hover: none; min-height: 350px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1.1rem;"><i data-lucide="star" style="color: var(--primary); display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Analisis Sebaran Rating Ulasan</h3>
                <div style="flex: 1; position: relative;">
                    <canvas id="ratingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<!-- Chart.js styles (if any) -->
@endsection

@section('scripts')
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Parse database values
        const salesTrendRaw = @json($salesTrend);
        const topProductsRaw = @json($topProducts);
        const orderFlowRaw = @json($orderFlow);
        const ratingsRaw = @json($ratings);

        // 1. Line Chart: Sales Trend
        const trendDates = salesTrendRaw.map(item => item.date);
        const trendSales = salesTrendRaw.map(item => parseFloat(item.total_sales));

        new Chart(document.getElementById('salesTrendChart'), {
            type: 'line',
            data: {
                labels: trendDates.length > 0 ? trendDates : ['Belum ada transaksi'],
                datasets: [{
                    label: 'Omzet Penjualan (Rp)',
                    data: trendSales.length > 0 ? trendSales : [0],
                    borderColor: '#5f5af6',
                    backgroundColor: 'rgba(95, 90, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });

        // 2. Bar Chart: Top Products
        const prodLabels = topProductsRaw.map(item => item.product ? item.product.name : 'Produk Terhapus');
        const prodQtys = topProductsRaw.map(item => parseInt(item.total_qty));

        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: prodLabels.length > 0 ? prodLabels : ['Belum ada produk terjual'],
                datasets: [{
                    label: 'Kuantitas Terjual',
                    data: prodQtys.length > 0 ? prodQtys : [0],
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
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 3. Doughnut Chart: Order Flow
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

        new Chart(document.getElementById('orderFlowChart'), {
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

        // 4. Bar Chart: Ratings Analysis
        // Construct array for stars 1 to 5
        const starCounts = [0, 0, 0, 0, 0]; // Index 0=1 star, 4=5 stars
        ratingsRaw.forEach(item => {
            const r = parseInt(item.rating);
            if (r >= 1 && r <= 5) {
                starCounts[r - 1] = parseInt(item.count);
            }
        });

        new Chart(document.getElementById('ratingsChart'), {
            type: 'bar',
            data: {
                labels: ['★1', '★2', '★3', '★4', '★5'],
                datasets: [{
                    label: 'Jumlah Ulasan',
                    data: starCounts,
                    backgroundColor: '#fbbf24',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endsection
