@extends('layouts.app')

@section('title', 'Checkout Pesanan')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 2rem;">
    <h1 style="font-size: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="shopping-bag" style="color: var(--primary);"></i>
        Checkout Belanjaan Anda
    </h1>

    <div style="display: grid; grid-template-columns: 1fr 420px; gap: 2rem; align-items: flex-start;">
        <!-- Shipping & Checkout Details Form -->
        <div class="glass-card" style="padding: 2rem; border-radius: var(--radius-md);">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i data-lucide="map-pin" style="display: inline-block; vertical-align: middle; width: 20px; height: 20px; margin-right: 0.25rem; color: var(--primary);"></i> Alamat Pengiriman
            </h3>

            <form action="{{ route('cart.processCheckout') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="store_id" value="{{ $store->id }}">
                <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="15000">

                <div class="form-group">
                    <label for="shipping_recipient_name" class="form-label">Nama Penerima</label>
                    <input type="text" name="shipping_recipient_name" id="shipping_recipient_name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="form-group">
                    <label for="shipping_recipient_phone" class="form-label">Nomor Telepon Penerima</label>
                    <input type="text" name="shipping_recipient_phone" id="shipping_recipient_phone" class="form-control" value="{{ auth()->user()->phone }}" placeholder="Contoh: 0812345678" required>
                </div>

                <div class="form-group">
                    <label for="shipping_address" class="form-label">Alamat Lengkap Pengiriman</label>
                    <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control" placeholder="Tuliskan alamat lengkap beserta kelurahan, kecamatan, dan kode pos" required>{{ $store->address }}</textarea>
                </div>

                <div class="grid-2" style="margin-bottom: 0; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="shipping_courier" class="form-label">Pilih Kurir Pengiriman</label>
                        <select name="shipping_courier" id="shipping_courier" class="form-control" required>
                            <option value="JNE Reguler" data-fee="15000">JNE Reguler (Rp 15.000)</option>
                            <option value="J&T Express" data-fee="18000">J&T Express (Rp 18.000)</option>
                            <option value="SiCepat Halu" data-fee="10000">SiCepat Halu (Rp 10.000)</option>
                            <option value="GoSend Instant" data-fee="25000">GoSend Instant (Rp 25.000)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan Pesanan (Opsional)</label>
                        <input type="text" name="notes" id="notes" class="form-control" placeholder="Contoh: warna cadangan, titip pos satpam">
                    </div>
                </div>

                <button type="submit" class="btn btn-accent" style="width: 100%; margin-top: 2rem; padding: 1rem;" onclick="return confirmAction(event, 'Apakah rincian pengiriman Anda sudah benar? Klik Oke untuk membuat pesanan.')">
                    <i data-lucide="check-square"></i> Buat Pesanan Sekarang
                </button>
            </form>
        </div>

        <!-- Checkout Summary Panel -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Items grouped -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    Produk dari Toko: <strong>{{ $store->name }}</strong>
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($storeItems as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                            <div style="max-width: 70%;">
                                <strong>{{ $item->product->name }}</strong> 
                                <span style="color: var(--text-muted);">x{{ $item->quantity }}</span>
                                @if($item->variant)
                                    <br><small style="color: var(--text-muted);">Varian: {{ $item->variant->name }}</small>
                                @endif
                            </div>
                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Price Breakdown Calculation -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 0.75rem;">
                <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">Total Pembayaran</h3>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Subtotal Barang:</span>
                    <strong id="subtotal-items" data-val="{{ $subtotal }}">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                </div>

                @if($discount > 0)
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--status-cancelled);">
                    <span>Potongan Voucher:</span>
                    <strong id="discount-val" data-val="{{ $discount }}">-Rp {{ number_format($discount, 0, ',', '.') }}</strong>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Ongkos Kirim:</span>
                    <strong id="shipping-fee-text">Rp 15.000</strong>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color);">

                <div style="display: flex; justify-content: space-between; font-size: 1.25rem;">
                    <span style="font-family: var(--font-heading); font-weight: 700;">Total Bayar:</span>
                    <strong id="final-total-text" style="color: var(--primary); font-family: var(--font-heading); font-weight: 800;">
                        Rp {{ number_format(max(0, $subtotal - $discount) + 15000, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const courierSelect = document.getElementById('shipping_courier');
        const shippingFeeText = document.getElementById('shipping_fee-text');
        const finalTotalText = document.getElementById('final-total-text');
        const shippingCostInput = document.getElementById('shipping_cost_input');
        
        const subtotal = parseInt(document.getElementById('subtotal-items').dataset.val);
        const discount = document.getElementById('discount-val') ? parseInt(document.getElementById('discount-val').dataset.val) : 0;

        function updateTotals() {
            const selectedOption = courierSelect.options[courierSelect.selectedIndex];
            const fee = parseInt(selectedOption.dataset.fee);
            
            // Update hidden input
            shippingCostInput.value = fee;
            
            // Update UI text
            document.getElementById('shipping-fee-text').innerText = formatRupiah(fee);
            
            // Compute total
            const finalTotal = Math.max(0, subtotal - discount) + fee;
            finalTotalText.innerText = formatRupiah(finalTotal);
        }

        courierSelect.addEventListener('change', updateTotals);
        updateTotals(); // Initial load
    });
</script>
@endsection
