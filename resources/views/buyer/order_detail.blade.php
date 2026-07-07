@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 2rem;">
    <!-- Header Summary -->
    <div class="glass-card" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span style="font-size: 0.85rem; color: var(--text-muted);">Nomor Pesanan</span>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;">{{ $order->order_number }}</h2>
            <span style="font-size: 0.85rem; color: var(--text-muted);">Tanggal Transaksi: {{ $order->created_at->format('d M Y H:i') }} WIB</span>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center;">
            <span class="badge badge-{{ $order->status }}" style="font-size: 0.9rem; padding: 0.4rem 1rem;">
                {{ strtoupper($order->status) }}
            </span>
            @if($order->status === 'completed')
                <a href="{{ route('orders.invoicePdf', $order->id) }}" target="_blank" class="btn btn-primary btn-sm"><i data-lucide="printer"></i> Cetak Invoice</a>
            @endif
        </div>
    </div>

    <!-- Status Timeline Tracking & Address info -->
    <div class="grid-2">
        <!-- Delivery info -->
        <div class="glass-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i data-lucide="truck" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Informasi Pengiriman</h3>
            <div>
                <strong>Penerima:</strong> {{ $order->shipping_recipient_name }} ({{ $order->shipping_recipient_phone }})<br>
                <strong>Alamat:</strong> {{ $order->shipping_address }}
            </div>
            <div>
                <strong>Kurir:</strong> {{ $order->shipping_courier }} (Estimasi: {{ $order->shipping_estimate }})<br>
                @if($order->tracking_number)
                    <strong>No. Resi Pengiriman:</strong> <span style="font-family: monospace; background: #e2e8f0; padding: 0.1rem 0.4rem; border-radius: 4px;">{{ $order->tracking_number }}</span>
                @else
                    <strong>No. Resi Pengiriman:</strong> <span style="color: var(--text-muted);">Belum diinput penjual</span>
                @endif
            </div>
        </div>

        <!-- History Timeline -->
        <div class="glass-card" style="padding: 1.5rem;">
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;"><i data-lucide="list" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Log Pelacakan Status</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem; border-left: 2px solid var(--border-color); padding-left: 1rem; margin-left: 0.5rem;">
                @foreach($order->histories as $history)
                    <div style="position: relative;">
                        <!-- Dot spacer -->
                        <div style="position: absolute; left: -21px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background-color: var(--primary);"></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $history->created_at->format('d M Y H:i') }}</div>
                        <strong style="display: block; font-size: 0.9rem; text-transform: uppercase;">{{ $history->status }}</strong>
                        <span style="font-size: 0.85rem; color: #475569;">{{ $history->notes }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Items list -->
    <div class="glass-card" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;"><i data-lucide="shopping-bag" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Rincian Barang</h3>
        
        <div class="table-responsive" style="margin-top: 0; border: none;">
            <table>
                <thead>
                    <tr>
                        <th>Item Produk</th>
                        <th style="text-align: right;">Harga Satuan</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="text-align: right;">Subtotal</th>
                        @if($order->status === 'completed')
                        <th style="text-align: center;">Ulasan Anda</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if($item->variant)
                                <br><span style="font-size: 0.75rem; background: #e2e8f0; padding: 0.1rem 0.4rem; border-radius: 4px;">Varian: {{ $item->variant->name }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">Rp {{ number_format($item->price - $item->discount_amount, 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        
                        <!-- Review form inside table cell if completed -->
                        @if($order->status === 'completed')
                        <td style="text-align: center;">
                            @if($item->review)
                                <div style="color: #fbbf24; display: flex; align-items: center; justify-content: center; gap: 0.1rem;">
                                    @for($i=1; $i<=5; $i++)
                                        <i data-lucide="star" style="width: 12px; height: 12px; fill: {{ $i <= $item->review->rating ? '#fbbf24' : 'none' }}; stroke: {{ $i <= $item->review->rating ? '#fbbf24' : '#cbd5e1' }};"></i>
                                    @endfor
                                </div>
                            @else
                                <!-- Button to trigger review panel below -->
                                <button type="button" class="btn btn-secondary btn-sm" onclick="showReviewForm({{ $item->id }}, '{{ addslashes($item->product->name) }}')">Beri Ulasan</button>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    
                    <!-- Breakdowns -->
                    <tr>
                        <td colspan="{{ $order->status === 'completed' ? 3 : 2 }}" style="border: none; text-align: right; color: var(--text-muted);">Subtotal Belanja:</td>
                        <td style="border: none; text-align: right; font-weight: 500;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        @if($order->status === 'completed') <td></td> @endif
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="{{ $order->status === 'completed' ? 3 : 2 }}" style="border: none; text-align: right; color: var(--status-cancelled);">Diskon Voucher:</td>
                        <td style="border: none; text-align: right; font-weight: 500; color: var(--status-cancelled);">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                        @if($order->status === 'completed') <td></td> @endif
                    </tr>
                    @endif
                    <tr>
                        <td colspan="{{ $order->status === 'completed' ? 3 : 2 }}" style="border: none; text-align: right; color: var(--text-muted);">Ongkos Kirim ({{ $order->shipping_courier }}):</td>
                        <td style="border: none; text-align: right; font-weight: 500;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        @if($order->status === 'completed') <td></td> @endif
                    </tr>
                    <tr style="font-size: 1.15rem; font-weight: bold;">
                        <td colspan="{{ $order->status === 'completed' ? 3 : 2 }}" style="text-align: right;">Total Tagihan:</td>
                        <td style="text-align: right; color: var(--primary);">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                        @if($order->status === 'completed') <td></td> @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Review Submission Panel (toggled dynamically) -->
    <div id="review-panel" class="glass-card" style="padding: 1.5rem; display: none;">
        <h3 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            Tulis Ulasan untuk: <span id="review-product-name" style="color: var(--primary);"></span>
        </h3>
        
        <form action="{{ route('buyer.submitReview') }}" method="POST">
            @csrf
            <input type="hidden" name="order_item_id" id="review_order_item_id" value="">
            
            <div class="form-group">
                <label class="form-label">Rating Bintang (1-5)</label>
                <div style="display: flex; gap: 0.5rem; font-size: 1.5rem; color: #cbd5e1; cursor: pointer;">
                    @for($i=1; $i<=5; $i++)
                        <i data-lucide="star" class="review-star" data-rating="{{ $i }}" style="width: 28px; height: 28px; fill: none; stroke: currentColor;" onclick="setRating({{ $i }})"></i>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating_input" value="" required>
            </div>

            <div class="form-group">
                <label for="comment" class="form-label">Komentar Ulasan</label>
                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Tuliskan pengalaman berbelanja Anda menggunakan produk ini..."></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary btn-sm">Kirim Ulasan</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hideReviewPanel()">Batal</button>
            </div>
        </form>
    </div>

    <!-- Payment Confirm Panel / Return Panel -->
    <div class="grid-2">
        <!-- Payment details & upload -->
        <div class="glass-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i data-lucide="credit-card" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Pembayaran Transaksi</h3>
            
            @if($order->status === 'pending')
                <div style="background-color: var(--primary-light); color: var(--primary); padding: 1rem; border-radius: var(--radius-md); font-size: 0.9rem;">
                    <strong>Petunjuk Pembayaran:</strong><br>
                    Silakan transfer nominal tagihan sebesar <strong>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</strong> ke rekening berikut:<br>
                    - <strong>Bank BCA:</strong> 123-456-7890 a/n TokoKita Admin<br>
                    - <strong>Bank Mandiri:</strong> 987-654-3210 a/n TokoKita Admin
                </div>

                @if(!$order->payment || $order->payment->status === 'pending' && !$order->payment->payment_receipt)
                    <form action="{{ route('buyer.submitPayment', $order->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
                        @csrf
                        <div class="form-group">
                            <label for="payment_method" class="form-label">Pilih Rekening Tujuan Transfer</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="Transfer Bank BCA">Transfer Bank BCA (123-456-7890)</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri (987-654-3210)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_receipt" class="form-label">Upload Bukti Transfer (Image Max 2MB)</label>
                            <input type="file" name="payment_receipt" id="payment_receipt" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="return confirmAction(event, 'Kirim bukti pembayaran ini?')">Kirim Bukti Pembayaran</button>
                    </form>
                @else
                    <div style="background-color: var(--status-pending-light); color: var(--status-pending); border: 1px solid var(--status-pending); padding: 1rem; border-radius: var(--radius-md); text-align: center; font-weight: 500;">
                        <i data-lucide="loader" class="animate-spin" style="display: inline-block; vertical-align: middle;"></i>
                        Bukti pembayaran diunggah. Menunggu verifikasi penjual.
                    </div>
                @endif

                <!-- Cancel Order -->
                <form action="{{ route('buyer.cancelOrder', $order->id) }}" method="POST" onsubmit="return confirmAction(event, 'Batalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; margin-top: 1rem;">Batalkan Pesanan Saya</button>
                </form>
            @else
                <div style="font-size: 0.95rem;">
                    <strong>Metode Pembayaran:</strong> {{ $order->payment ? $order->payment->payment_method : '-' }}<br>
                    <strong>Status Pembayaran:</strong> 
                    <span class="badge badge-{{ $order->payment ? $order->payment->status : 'pending' }}">
                        {{ $order->payment ? $order->payment->status : 'pending' }}
                    </span>
                    @if($order->payment && $order->payment->payment_receipt)
                        <div style="margin-top: 1rem;">
                            <strong>Bukti Pembayaran:</strong><br>
                            <a href="{{ asset($order->payment->payment_receipt) }}" target="_blank" style="color: var(--primary); text-decoration: underline; font-size: 0.85rem;">Lihat Bukti Transfer Unggahan</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Returns Section -->
        @if($order->status === 'completed' || $order->status === 'shipped' || $order->status === 'returned')
        <div class="glass-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;"><i data-lucide="refresh-ccw" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Pengajuan Retur Barang</h3>
            
            @if($order->returns->isEmpty())
                <p style="color: var(--text-muted); font-size: 0.9rem;">Apakah barang yang Anda terima rusak atau tidak sesuai? Anda dapat mengajukan retur ke Admin.</p>
                
                <form action="{{ route('buyer.submitReturn', $order->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div class="form-group">
                        <label for="reason" class="form-label">Alasan Retur</label>
                        <textarea name="reason" id="reason" rows="2" class="form-control" placeholder="Tulis rincian komplain..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="evidence_image" class="form-label">Foto Bukti (Kondisi Barang rusak)</label>
                        <input type="file" name="evidence_image" id="evidence_image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="border-color: var(--status-returned); color: var(--status-returned);" onclick="return confirmAction(event, 'Kirim pengajuan komplain retur?')">Kirim Pengajuan Retur</button>
                </form>
            @else
                @php $ret = $order->returns->first(); @endphp
                <div style="font-size: 0.95rem; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md);">
                    <strong>Status Pengajuan Retur:</strong> 
                    <span class="badge badge-{{ $ret->status === 'approved' ? 'completed' : ($ret->status === 'rejected' ? 'cancelled' : 'pending') }}">
                        {{ strtoupper($ret->status) }}
                    </span><br>
                    <strong>Alasan Retur:</strong> {{ $ret->reason }}<br>
                    @if($ret->evidence_image)
                        <strong>Foto Bukti:</strong> <a href="{{ asset($ret->evidence_image) }}" target="_blank" style="color: var(--primary); text-decoration: underline;">Lihat Bukti Foto</a><br>
                    @endif
                    @if($ret->admin_notes)
                        <div style="background-color: #f8fafc; border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 4px; margin-top: 0.5rem; font-size: 0.85rem;">
                            <strong>Catatan Admin:</strong> {{ $ret->admin_notes }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const reviewPanel = document.getElementById('review-panel');
    const reviewProductName = document.getElementById('review-product-name');
    const reviewOrderItemIdInput = document.getElementById('review_order_item_id');
    const ratingInput = document.getElementById('rating_input');
    const stars = document.querySelectorAll('.review-star');

    function showReviewForm(orderItemId, productName) {
        reviewPanel.style.display = 'block';
        reviewProductName.innerText = productName;
        reviewOrderItemIdInput.value = orderItemId;
        
        // Scroll to review panel
        reviewPanel.scrollIntoView({ behavior: 'smooth' });
    }

    function hideReviewPanel() {
        reviewPanel.style.display = 'none';
        ratingInput.value = '';
        stars.forEach(star => {
            star.style.fill = 'none';
            star.style.color = '#cbd5e1';
        });
    }

    function setRating(rating) {
        ratingInput.value = rating;
        stars.forEach(star => {
            const starVal = parseInt(star.dataset.rating);
            if (starVal <= rating) {
                star.style.fill = '#fbbf24';
                star.style.color = '#fbbf24';
            } else {
                star.style.fill = 'none';
                star.style.color = '#cbd5e1';
            }
        });
    }
</script>
@endsection
