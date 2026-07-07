@extends('layouts.app')

@section('title', 'Detail Kelola Pesanan #' . $order->order_number)

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
                <a href="{{ route('seller.orders') }}" class="sidebar-link active">
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
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem;">Rincian Pesanan #{{ $order->order_number }}</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Tanggal Pesanan Masuk: {{ $order->created_at->format('d M Y H:i') }} WIB</p>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('seller.deliveryNotePdf', $order->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i data-lucide="truck"></i> Surat Jalan (PDF)</a>
                <a href="{{ route('orders.invoicePdf', $order->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i data-lucide="printer"></i> Cetak Invoice</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: flex-start;">
            <!-- Main details block -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Product Items -->
                <div class="glass-card" style="padding: 1.5rem; hover: none;">
                    <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">Daftar Barang Belanjaan</h3>
                    
                    <div class="table-responsive" style="margin-top: 0; border: none;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th style="text-align: right;">Harga Satuan</th>
                                    <th style="text-align: center;">Jumlah</th>
                                    <th style="text-align: right;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        @if($item->variant)
                                            <br><small style="color: var(--text-muted);">Varian: {{ $item->variant->name }}</small>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">Rp {{ number_format($item->price - $item->discount_amount, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">{{ $item->quantity }} pcs</td>
                                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                
                                <tr>
                                    <td colspan="3" style="text-align: right; border: none; color: var(--text-muted);">Subtotal:</td>
                                    <td style="text-align: right; border: none; font-weight: 500;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" style="text-align: right; border: none; color: var(--status-cancelled);">Potongan Voucher:</td>
                                    <td style="text-align: right; border: none; font-weight: 500; color: var(--status-cancelled);">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" style="text-align: right; border: none; color: var(--text-muted);">Ongkir ({{ $order->shipping_courier }}):</td>
                                    <td style="text-align: right; border: none; font-weight: 500;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr style="font-size: 1.15rem; font-weight: bold;">
                                    <td colspan="3" style="text-align: right;">Total Transaksi:</td>
                                    <td style="text-align: right; color: var(--primary);">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Details Panel -->
                <div class="glass-card" style="padding: 1.5rem; hover: none;">
                    <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">Status Pembayaran</h3>
                    
                    @if($order->payment)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <div>
                                <strong>Metode:</strong> {{ $order->payment->payment_method }}<br>
                                <strong>Nominal:</strong> Rp {{ number_format($order->payment->amount, 0, ',', '.') }}<br>
                                <strong>Status Verifikasi:</strong> 
                                <span class="badge badge-{{ $order->payment->status }}">
                                    {{ $order->payment->status }}
                                </span>
                            </div>
                            
                            <div>
                                @if($order->payment->payment_receipt)
                                    <strong>Bukti Transfer:</strong><br>
                                    <a href="{{ asset($order->payment->payment_receipt) }}" target="_blank">
                                        <img src="{{ asset($order->payment->payment_receipt) }}" alt="Bukti Transfer" style="width: 100%; max-height: 150px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); object-fit: contain; margin-top: 0.5rem;">
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">Belum mengunggah bukti pembayaran.</span>
                                @endif
                            </div>
                        </div>

                        <!-- Confirm payment form (active when pending and receipt is present) -->
                        @if($order->status === 'pending' && $order->payment->status === 'pending' && $order->payment->payment_receipt)
                            <form action="{{ route('seller.confirmPayment', $order->id) }}" method="POST" style="margin-top: 1.5rem;" onsubmit="return confirmAction(event, 'Konfirmasi pembayaran ini?')">
                                @csrf
                                <div style="background-color: var(--primary-light); color: var(--primary); padding: 1rem; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 1rem;">
                                    Periksa gambar bukti transfer di atas dengan teliti. Jika dana sudah masuk ke rekening Anda, silakan klik tombol konfirmasi di bawah ini.
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    <i data-lucide="check-circle-2"></i> Konfirmasi Pembayaran Valid
                                </button>
                            </form>
                        @endif
                    @else
                        <p style="color: var(--text-muted);">Informasi pembayaran belum tersedia.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar control block -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Status Actions -->
                <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">Tindakan Pesanan</h3>
                    
                    <div style="margin-bottom: 1rem;">
                        Status saat ini: <span class="badge badge-{{ $order->status }}" style="font-size: 0.85rem;">{{ strtoupper($order->status) }}</span>
                    </div>

                    @if($order->status === 'processing')
                        <!-- Shipping form -->
                        <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;" onsubmit="return confirmAction(event, 'Kirim barang pesanan ini sekarang?')">
                            @csrf
                            <input type="hidden" name="status" value="shipped">
                            
                            <div class="form-group">
                                <label for="tracking_number" class="form-label" style="font-weight: 600;">Nomor Resi Pengiriman ({{ $order->shipping_courier }})</label>
                                <input type="text" name="tracking_number" id="tracking_number" class="form-control" placeholder="Contoh: RESI998877" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes" class="form-label">Catatan Pengiriman</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Cth: Paket diserahkan ke kurir JNE pukul 15.00"></textarea>
                            </div>

                            <button type="submit" class="btn btn-accent"><i data-lucide="send"></i> Kirim Barang & Update Resi</button>
                        </form>
                    @endif

                    <!-- Change Order Statuses directly (for edge cases) -->
                    <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST" style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem;" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin mengganti status pesanan secara manual?')">
                        @csrf
                        <div class="form-group">
                            <label for="status" class="form-label">Ubah Status Manual</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Perbarui Status</button>
                    </form>
                </div>

                <!-- Shipping Address Info Box -->
                <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md); font-size: 0.9rem;">
                    <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">Alamat Kirim</h3>
                    <strong>Penerima:</strong> {{ $order->shipping_recipient_name }}<br>
                    <strong>Telepon:</strong> {{ $order->shipping_recipient_phone }}<br>
                    <strong>Alamat:</strong> {{ $order->shipping_address }}<br>
                    <strong>Kurir:</strong> {{ $order->shipping_courier }}<br>
                    <strong>Estimasi:</strong> {{ $order->shipping_estimate }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
