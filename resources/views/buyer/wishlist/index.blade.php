@extends('layouts.app')

@section('title', 'Daftar Keinginan')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 2rem; flex: 1;">
    <h1 style="font-size: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="heart" style="color: var(--status-cancelled); fill: var(--status-cancelled);"></i>
        Daftar Keinginan Saya
    </h1>

    @if($wishlists->isEmpty())
        <div class="glass-card" style="text-align: center; padding: 4rem 2rem;">
            <i data-lucide="heart-off" style="width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 1.5rem; opacity: 0.5;"></i>
            <h3>Daftar Keinginan Kosong</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 1.5rem;">Anda belum menyimpan produk apapun ke daftar keinginan.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Mulai Cari Produk</a>
        </div>
    @else
        <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
            <div class="table-responsive" style="margin-top: 0; border: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Nama Toko</th>
                            <th style="text-align: right;">Harga</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wishlists as $wishlist)
                        <tr>
                            <td style="width: 80px;">
                                @if($wishlist->product->image)
                                    <img src="{{ asset($wishlist->product->image) }}" alt="{{ $wishlist->product->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-sm);">
                                @else
                                    <div style="width: 60px; height: 60px; background-color: var(--text-muted); opacity: 0.2; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="image" style="width: 20px; height: 20px;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('product.show', $wishlist->product->slug) }}" style="font-weight: 600; text-decoration: underline; color: inherit;">
                                    {{ $wishlist->product->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('store.show', $wishlist->product->store->slug) }}" style="text-decoration: underline; font-weight: 500;">
                                    {{ $wishlist->product->store->name }}
                                </a>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary);">
                                Rp {{ number_format($wishlist->product->discounted_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="{{ route('product.show', $wishlist->product->slug) }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
                                
                                <form action="{{ route('wishlist.destroy', $wishlist->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini dari daftar keinginan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--status-cancelled); border-color: rgba(220, 38, 38, 0.2); background: rgba(220, 38, 38, 0.05);">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $wishlists->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
