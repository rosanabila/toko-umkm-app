@extends('layouts.app')

@section('title', 'Katalog Produk UMKM')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-main" style="padding: 4rem 2rem; color: white; text-align: center; position: relative; overflow: hidden; border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); margin-bottom: 2rem;">
    <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05); filter: blur(20px);"></div>
    <div style="position: absolute; bottom: -50px; right: -50px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.08); filter: blur(30px);"></div>
    
    <div style="max-width: 800px; margin: 0 auto; z-index: 1; position: relative;">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem; color: white; line-height: 1.2;">Dukung UMKM Lokal Bersama <span style="border-bottom: 4px solid var(--accent);">TokoKita</span></h1>
        <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 2rem; font-weight: 400;">Temukan produk berkualitas tinggi langsung dari produsen lokal terbaik di sekitar Anda.</p>
    </div>
</div>

<!-- Main Catalog Container -->
<div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem 4rem; display: flex; flex-direction: column; gap: 2rem;">
    <!-- Filter Panel -->
    <div class="glass-card" style="padding: 1.5rem;">
        <form action="{{ route('home') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="flex: 2; min-width: 200px; margin-bottom: 0;">
                <label for="search" class="form-label">Cari Produk</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Ketik nama produk..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom: 0;">
                <label for="price_min" class="form-label">Min Harga (Rp)</label>
                <input type="number" name="price_min" id="price_min" class="form-control" placeholder="Min" value="{{ request('price_min') }}">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom: 0;">
                <label for="price_max" class="form-label">Max Harga (Rp)</label>
                <input type="number" name="price_max" id="price_max" class="form-control" placeholder="Max" value="{{ request('price_max') }}">
            </div>
            
            <div style="display: flex; gap: 0.5rem; min-width: 200px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.75rem;"><i data-lucide="search" style="width: 16px; height: 16px;"></i> Filter</button>
                @if(request()->anyFilled(['search', 'category', 'price_min', 'price_max']))
                    <a href="{{ route('home') }}" class="btn btn-secondary" style="padding: 0.75rem;"><i data-lucide="x" style="width: 16px; height: 16px;"></i> Bersihkan</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Catalog Section -->
    <div>
        <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="grid" style="color: var(--primary);"></i>
            Katalog Produk
        </h2>

        @if($products->isEmpty())
            <div class="glass-card" style="text-align: center; padding: 4rem 2rem;">
                <i data-lucide="info" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <h3>Produk Tidak Ditemukan</h3>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Silakan coba kata kunci pencarian atau filter kategori lainnya.</p>
                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 1.5rem;">Lihat Semua Produk</a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $prod)
                <div class="product-card">
                    <div class="product-img">
                        @if($prod->discount_percent > 0)
                            <div class="discount-tag">Diskon {{ number_format($prod->discount_percent, 0) }}%</div>
                        @endif
                        <i data-lucide="package" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                    </div>
                    
                    <div class="product-info">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="badge badge-processing" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">{{ $prod->category->name }}</span>
                            <!-- Rating -->
                            <div class="rating-stars">
                                @php $avg = $prod->average_rating; @endphp
                                @for($i=1; $i<=5; $i++)
                                    <i data-lucide="star" style="width: 12px; height: 12px; fill: {{ $i <= $avg ? '#fbbf24' : 'none' }}; stroke: {{ $i <= $avg ? '#fbbf24' : '#cbd5e1' }};"></i>
                                @endfor
                                <span style="font-size: 11px; color: var(--text-muted); margin-left: 2px;">({{ $prod->reviews->count() }})</span>
                            </div>
                        </div>

                        <h3 class="product-name"><a href="{{ route('product.show', $prod->slug) }}">{{ $prod->name }}</a></h3>
                        
                        <div class="product-store">
                            <i data-lucide="store" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i>
                            <a href="{{ route('store.show', $prod->store->slug) }}" style="text-decoration: underline;">{{ $prod->store->name }}</a>
                        </div>
                        
                        <div class="product-price-row">
                            @if($prod->discount_percent > 0)
                                <div class="price-original">Rp {{ number_format($prod->price, 0, ',', '.') }}</div>
                            @endif
                            <div class="price-final">Rp {{ number_format($prod->discounted_price, 0, ',', '.') }}</div>
                        </div>

                        <a href="{{ route('product.show', $prod->slug) }}" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width: 100%; border-color: var(--primary); color: var(--primary);">Lihat Detail</a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div style="margin-top: 3rem; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
