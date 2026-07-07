@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 2rem;">
    <h1 style="font-size: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="shopping-cart" style="color: var(--primary);"></i>
        Keranjang Belanja Anda
    </h1>

    @if($cartItems->isEmpty())
        <div class="glass-card" style="text-align: center; padding: 4rem 2rem;">
            <i data-lucide="shopping-cart" style="width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 1.5rem; opacity: 0.5;"></i>
            <h3>Keranjang Anda Masih Kosong</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 1.5rem;">Silakan jelajahi katalog produk kami untuk mulai menambahkan barang belanjaan.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: flex-start;">
            <!-- Cart Items List -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: right;">Harga</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: right;">Subtotal</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <div style="display: flex; gap: 1rem; align-items: center;">
                                        <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                            <i data-lucide="package" style="width: 20px; height: 20px; opacity: 0.4;"></i>
                                        </div>
                                        <div>
                                            <strong style="display: block; font-size: 0.95rem;">
                                                <a href="{{ route('product.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                            </strong>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                                Toko: <strong>{{ $item->product->store->name }}</strong>
                                            </span>
                                            @if($item->variant)
                                                <br><span style="font-size: 0.75rem; background: #e2e8f0; padding: 0.1rem 0.4rem; border-radius: 4px;">Varian: {{ $item->variant->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @php
                                        $price = $item->product->discounted_price + ($item->variant ? $item->variant->additional_price : 0.00);
                                    @endphp
                                    Rp {{ number_format($price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" style="width: 60px; text-align: center;" class="form-control">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.4rem 0.5rem;"><i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i></button>
                                    </form>
                                </td>
                                <td style="text-align: right; font-weight: 600; white-space: nowrap;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirmAction(event, 'Hapus produk ini dari keranjang?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.4rem 0.6rem;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cart Summary Panel -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Voucher Box -->
                <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Kupon Voucher Belanja</h3>
                    
                    <form action="{{ route('cart.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="voucher_code" class="form-control" placeholder="Ketik kode kupon..." value="{{ session('voucher_code') }}" style="text-transform: uppercase;">
                        <button type="submit" class="btn btn-secondary">Klaim</button>
                    </form>
                    
                    @if(session('voucher_code'))
                        <div style="margin-top: 0.75rem; background: var(--status-completed-light); color: var(--status-completed); border: 1px solid var(--status-completed); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>Kupon <strong>{{ session('voucher_code') }}</strong> aktif!</span>
                            <form action="{{ route('cart.index') }}" method="GET" style="display: inline;">
                                <input type="hidden" name="voucher_code" value="">
                                <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; display: flex; align-items: center;"><i data-lucide="x-circle" style="width: 14px; height: 14px;"></i></button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Price Breakdowns -->
                <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 1rem;">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Ringkasan Belanja</h3>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                        <span style="color: var(--text-muted);">Total Belanja Item:</span>
                        <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>

                    @if($discount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem; color: var(--status-cancelled);">
                        <span>Diskon Voucher:</span>
                        <strong>-Rp {{ number_format($discount, 0, ',', '.') }}</strong>
                    </div>
                    @endif

                    <hr style="border: 0; border-top: 1px solid var(--border-color);">

                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem;">
                        <span style="font-family: var(--font-heading); font-weight: 700;">Subtotal Akhir:</span>
                        <strong style="color: var(--primary); font-family: var(--font-heading); font-weight: 800;">
                            Rp {{ number_format($finalTotal, 0, ',', '.') }}
                        </strong>
                    </div>

                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 1rem;">
                        Lanjut ke Checkout <i data-lucide="arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
