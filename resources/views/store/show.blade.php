@extends('layouts.app')

@section('title', $store->name)

@section('content')
<!-- Store Banner -->
<div class="bg-gradient-main" style="padding: 3rem 2rem; color: white; border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); margin-bottom: 2rem;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 2rem; align-items: center;">
        <!-- Logo -->
        <div style="width: 100px; height: 100px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; border: 2px solid white;">
            @if($store->logo)
                <img src="{{ asset($store->logo) }}" alt="Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                {{ substr($store->name, 0, 1) }}
            @endif
        </div>
        
        <!-- Info details -->
        <div style="flex: 1; min-width: 250px; display: flex; flex-direction: column; gap: 0.5rem;">
            <h1 style="color: white; font-size: 2.5rem; font-weight: 800; line-height: 1.2;">{{ $store->name }}</h1>
            <p style="opacity: 0.9; max-width: 700px;">{{ $store->description ?: 'Selamat datang di toko kami!' }}</p>
            
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem;">
                <span><i data-lucide="map-pin" style="width: 14px; height: 14px; display: inline; vertical-align: middle; margin-right: 0.2rem;"></i> {{ $store->address ?: 'Alamat belum diset.' }}</span>
                <span><i data-lucide="clock" style="width: 14px; height: 14px; display: inline; vertical-align: middle; margin-right: 0.2rem;"></i> Operasional: {{ substr($store->operating_hours_open, 0, 5) }} - {{ substr($store->operating_hours_close, 0, 5) }}</span>
                <span><i data-lucide="user-check" style="width: 14px; height: 14px; display: inline; vertical-align: middle; margin-right: 0.2rem;"></i> Pemilik: {{ $store->user->name }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Catalog Products -->
<div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem 4rem; display: grid; grid-template-columns: 280px 1fr; gap: 2rem;">
    <!-- Sidebar Store Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="glass-card" style="padding: 1.5rem; font-size: 0.9rem;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Info Pengiriman</h3>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <strong style="color: #475569;">Area Pengiriman:</strong>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.25rem;">
                        @if(!empty($store->shipping_areas))
                            @foreach($store->shipping_areas as $area)
                                <span class="badge badge-processing" style="font-size: 0.7rem;">{{ $area }}</span>
                            @endforeach
                        @else
                            <span class="badge badge-cancelled" style="font-size: 0.7rem;">Nasional / Umum</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <strong style="color: #475569;">Kontak Toko:</strong>
                    <div style="margin-top: 0.25rem;">{{ $store->phone ?: $store->user->phone ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog grid -->
    <div>
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="package" style="color: var(--primary);"></i>
            Koleksi Produk Toko
        </h2>

        @if($products->isEmpty())
            <div class="glass-card" style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                Toko ini belum mengunggah produk belanjaan apapun.
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $prod)
                <div class="product-card">
                    <div class="product-img" style="height: 160px;">
                        @if($prod->discount_percent > 0)
                            <div class="discount-tag">Diskon {{ number_format($prod->discount_percent, 0) }}%</div>
                        @endif
                        <i data-lucide="package" style="width: 40px; height: 40px; opacity: 0.4;"></i>
                    </div>
                    
                    <div class="product-info">
                        <span class="badge badge-processing" style="font-size: 0.65rem; width: fit-content;">{{ $prod->category->name }}</span>
                        <h3 class="product-name" style="font-size: 1.05rem; margin: 0.5rem 0;"><a href="{{ route('product.show', $prod->slug) }}">{{ $prod->name }}</a></h3>
                        
                        <div class="product-price-row">
                            @if($prod->discount_percent > 0)
                                <div class="price-original">Rp {{ number_format($prod->price, 0, ',', '.') }}</div>
                            @endif
                            <div class="price-final">Rp {{ number_format($prod->discounted_price, 0, ',', '.') }}</div>
                        </div>

                        <a href="{{ route('product.show', $prod->slug) }}" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width: 100%; border-color: var(--primary); color: var(--primary);">Beli Produk</a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div style="margin-top: 3rem; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
