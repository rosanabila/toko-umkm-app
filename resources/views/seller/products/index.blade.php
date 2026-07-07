@extends('layouts.app')

@section('title', 'Manajemen Produk Toko')

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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem;">Manajemen Produk</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Tambahkan produk baru, atur stok barang, varian harga, dan diskon promo.</p>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('seller.reports.stockPdf') }}" target="_blank" class="btn btn-secondary btn-sm"><i data-lucide="printer"></i> Cetak Laporan Stok (PDF)</a>
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> Tambah Produk</a>
            </div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($products->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 4rem 2rem;">Belum ada produk di toko Anda. Klik "Tambah Produk" untuk mulai berjualan!</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th style="text-align: right;">Harga Base</th>
                                <th style="text-align: center;">Diskon</th>
                                <th style="text-align: right;">Harga Final</th>
                                <th style="text-align: center;">Stok</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->variants->isNotEmpty())
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                                            Varian: 
                                            @foreach($product->variants as $variant)
                                                {{ $variant->name }} (Rp {{ number_format($variant->additional_price + $product->discounted_price, 0, ',', '.') }}, Stok: {{ $variant->stock }}){{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge badge-processing">{{ $product->category->name }}</span></td>
                                <td style="text-align: right;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ $product->discount_percent > 0 ? $product->discount_percent . '%' : '-' }}</td>
                                <td style="text-align: right; font-weight: 600; color: var(--primary);">
                                    Rp {{ number_format($product->discounted_price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    @if($product->variants->isNotEmpty())
                                        {{ $product->variants->sum('stock') }} (Varian)
                                    @else
                                        {{ $product->stock }}
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem;"><i data-lucide="edit-2" style="width: 14px; height: 14px;"></i></a>
                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus produk ini dari toko?')">
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
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
