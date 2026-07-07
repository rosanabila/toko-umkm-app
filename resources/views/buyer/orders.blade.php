@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 2rem; flex: 1;">
    <h1 style="font-size: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="package-open" style="color: var(--primary);"></i>
        Riwayat Pesanan Anda
    </h1>

    @if($orders->isEmpty())
        <div class="glass-card" style="text-align: center; padding: 4rem 2rem;">
            <i data-lucide="clipboard-list" style="width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 1.5rem; opacity: 0.5;"></i>
            <h3>Belum Ada Transaksi</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 1.5rem;">Anda belum pernah melakukan checkout pesanan produk di TokoKita.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @else
        <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
            <div class="table-responsive" style="margin-top: 0; border: none;">
                <table>
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal Belanja</th>
                            <th>Nama Toko</th>
                            <th style="text-align: right;">Total Bayar</th>
                            <th style="text-align: center;">Status Pesanan</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td style="color: var(--text-muted);">{{ $order->created_at->format('d M Y H:i') }} WIB</td>
                            <td>
                                <a href="{{ route('store.show', $order->store->slug) }}" style="text-decoration: underline; font-weight: 500;">
                                    {{ $order->store->name }}
                                </a>
                            </td>
                            <td style="text-align: right; font-weight: 600;">
                                Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-{{ $order->status }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="{{ route('buyer.orderDetail', $order->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                                @if($order->status === 'completed')
                                    <a href="{{ route('orders.invoicePdf', $order->id) }}" target="_blank" class="btn btn-primary btn-sm" style="background-color: var(--status-completed); box-shadow: none;"><i data-lucide="printer" style="width: 12px; height: 12px;"></i> Invoice</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
