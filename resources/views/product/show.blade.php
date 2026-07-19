@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem; display: flex; flex-direction: column; gap: 3rem;">
    <!-- Product Presentation -->
    <div class="glass-card" style="display: flex; flex-wrap: wrap; gap: 3rem; padding: 2.5rem;">
        <!-- Image block -->
        <div style="flex: 1; min-width: 300px; max-width: 500px; display: flex; flex-direction: column; gap: 1rem;">
            <div style="height: 350px; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; position: relative; border: 1px solid var(--border-color);">
                @if($product->discount_percent > 0)
                    <div class="discount-tag" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; top: 15px; right: 15px;">Diskon {{ number_format($product->discount_percent, 0) }}%</div>
                @endif
                <i data-lucide="package" style="width: 80px; height: 80px; opacity: 0.3; color: var(--text-main);"></i>
            </div>
        </div>

        <!-- Purchase & Info block -->
        <div style="flex: 1.2; min-width: 300px; display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <span class="badge badge-processing" style="margin-bottom: 0.75rem;">{{ $product->category->name }}</span>
                <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.2; margin-bottom: 0.5rem;">{{ $product->name }}</h1>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Rating -->
                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                        @php $avg = $product->average_rating; @endphp
                        @for($i=1; $i<=5; $i++)
                            <i data-lucide="star" style="width: 16px; height: 16px; fill: {{ $i <= $avg ? '#fbbf24' : 'none' }}; stroke: {{ $i <= $avg ? '#fbbf24' : '#cbd5e1' }};"></i>
                        @endfor
                        <strong style="margin-left: 0.25rem; font-size: 0.95rem;">{{ number_format($avg, 1) }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem;">({{ $reviews->count() }} Ulasan)</span>
                    </div>

                    <span style="color: var(--border-color);">|</span>
                    
                    <div>
                        Toko: <a href="{{ route('store.show', $product->store->slug) }}" style="color: var(--primary); font-weight: 600; text-decoration: underline;">{{ $product->store->name }}</a>
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color);">

            <!-- Pricing block -->
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                @if($product->discount_percent > 0)
                    <span style="text-decoration: line-through; color: var(--text-muted); font-size: 1.1rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
                <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                    <span style="font-size: 2.25rem; font-weight: 800; color: var(--primary); font-family: var(--font-heading);">
                        Rp {{ number_format($product->discounted_price, 0, ',', '.') }}
                    </span>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">/ unit</span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #475569;">Deskripsi Produk:</h3>
                <p style="color: #475569; line-height: 1.6; font-size: 0.95rem;">
                    {{ $product->description ?: 'Tidak ada deskripsi untuk produk ini.' }}
                </p>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color);">

            <!-- Purchase forms -->
            @auth
                @if(auth()->user()->isPembeli())
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                        @csrf
                        
                        <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                            <!-- Variant Selection -->
                            @if($product->variants->isNotEmpty())
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="variant_id" class="form-label" style="font-weight: 600;">Pilih Varian Produk</label>
                                <select name="variant_id" id="variant_id" class="form-control" required>
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}">
                                            {{ $variant->name }} 
                                            @if($variant->additional_price > 0)
                                                (+ Rp {{ number_format($variant->additional_price, 0, ',', '.') }})
                                            @endif
                                            (Stok: {{ $variant->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Quantity Selector -->
                            <div class="form-group" style="margin-bottom: 0; width: 120px;">
                                <label for="quantity" class="form-label" style="font-weight: 600;">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}" required>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem;">
                                <i data-lucide="shopping-cart"></i> Tambah ke Keranjang
                            </button>
                            <span style="font-size: 0.9rem; color: var(--text-muted);">
                                Stok tersedia: <strong>{{ $product->stock }}</strong> unit
                            </span>
                        </div>
                    </form>
                    
                    <form action="{{ route('wishlist.store') }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: var(--status-cancelled); border-color: rgba(220, 38, 38, 0.2); background: rgba(220, 38, 38, 0.02);">
                            <i data-lucide="heart" style="width: 16px; height: 16px; fill: var(--status-cancelled);"></i> Simpan ke Wishlist
                        </button>
                    </form>
                @else
                    <div style="background-color: var(--primary-light); color: var(--primary); padding: 1rem; border-radius: var(--radius-md); text-align: center; font-weight: 500;">
                        <i data-lucide="info" style="display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i>
                        Gunakan akun Pembeli untuk membeli produk ini. Akun Anda saat ini ber-role <strong>{{ strtoupper(auth()->user()->role) }}</strong>.
                    </div>
                @endif
            @else
                <div style="background-color: #f8fafc; border: 1px solid var(--border-color); padding: 1.5rem; border-radius: var(--radius-md); text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <p style="color: var(--text-muted); font-weight: 500;">Silakan masuk atau mendaftar terlebih dahulu untuk dapat membeli produk ini.</p>
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar Akun</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <!-- Reviews Block -->
    <div>
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="message-square" style="color: var(--primary);"></i>
            Ulasan Pembeli ({{ $reviews->count() }})
        </h2>

        @if($reviews->isEmpty())
            <div class="glass-card" style="text-align: center; padding: 3rem 2rem; color: var(--text-muted);">
                Belum ada ulasan untuk produk ini.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @foreach($reviews as $rev)
                <div class="glass-card" style="padding: 1.5rem; hover: none;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <div>
                            <strong style="font-size: 1.05rem;">{{ $rev->user->name }}</strong>
                            <div class="rating-stars" style="margin-top: 0.25rem;">
                                @for($i=1; $i<=5; $i++)
                                    <i data-lucide="star" style="width: 14px; height: 14px; fill: {{ $i <= $rev->rating ? '#fbbf24' : 'none' }}; stroke: {{ $i <= $rev->rating ? '#fbbf24' : '#cbd5e1' }};"></i>
                                @endfor
                            </div>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5;">
                        {{ $rev->comment ?: 'Pembeli tidak meninggalkan komentar ulasan.' }}
                    </p>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div>
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="sparkles" style="color: var(--primary);"></i>
            Produk Terkait Kategori
        </h2>
        <div class="product-grid">
            @foreach($relatedProducts as $rel)
            <div class="product-card">
                <div class="product-img" style="height: 140px;">
                    @if($rel->discount_percent > 0)
                        <div class="discount-tag">Diskon {{ number_format($rel->discount_percent, 0) }}%</div>
                    @endif
                    <i data-lucide="package" style="width: 32px; height: 32px; opacity: 0.4;"></i>
                </div>
                <div class="product-info" style="padding: 1rem; gap: 0.25rem;">
                    <h3 class="product-name" style="font-size: 1rem;"><a href="{{ route('product.show', $rel->slug) }}">{{ $rel->name }}</a></h3>
                    <div class="product-price-row">
                        <div class="price-final" style="font-size: 1.1rem;">Rp {{ number_format($rel->discounted_price, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
