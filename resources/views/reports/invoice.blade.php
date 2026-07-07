<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
        .header-table, .details-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #5f5af6;
        }
        .meta-info {
            text-align: right;
        }
        .details-table td {
            width: 50%;
            padding: 6px;
            vertical-align: top;
            border: 1px solid #eee;
            background: #fafafa;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
            color: #666;
        }
        .items-table th {
            background: #5f5af6;
            color: #ffffff;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .totals-table td {
            padding: 6px;
            text-align: right;
        }
        .totals-table td.label {
            width: 80%;
            font-weight: bold;
        }
        .totals-table td.value {
            width: 20%;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d1fae5; color: #10b981; }
        .badge-warning { background: #fef9c3; color: #eab308; }
        .badge-danger { background: #fee2e2; color: #ef4444; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="title">TokoKita</div>
                    <div>Faktur Pembelian Resmi</div>
                </td>
                <td class="meta-info">
                    <strong>No. Pesanan:</strong> {{ $order->order_number }}<br>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }} WIB<br>
                    <strong>Status Transaksi:</strong> 
                    <span class="badge {{ $order->status === 'completed' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Penerbit (Penjual):</div>
                    <strong>{{ $order->store->name }}</strong><br>
                    Telepon: {{ $order->store->phone ?? $order->store->user->phone }}<br>
                    Alamat: {{ $order->store->address }}
                </td>
                <td>
                    <div class="section-title">Tujuan Pengiriman (Pembeli):</div>
                    <strong>{{ $order->shipping_recipient_name }}</strong><br>
                    Telepon: {{ $order->shipping_recipient_phone }}<br>
                    Alamat: {{ $order->shipping_address }}
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product->name }}
                        @if($item->variant)
                            <br><small style="color: #666;">Varian: {{ $item->variant->name }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($item->price - $item->discount_amount, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal Belanja:</td>
                <td class="value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td class="label" style="color: #ef4444;">Potongan Diskon (Voucher):</td>
                <td class="value" style="color: #ef4444;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Ongkos Kirim ({{ $order->shipping_courier }}):</td>
                <td class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-size: 15px; font-weight: bold; border-top: 2px solid #333;">
                <td class="label">Total Pembayaran:</td>
                <td class="value" style="color: #5f5af6;">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Payment Info -->
        <div style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <div class="section-title">Informasi Pembayaran:</div>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%;"><strong>Metode:</strong> {{ $order->payment ? $order->payment->payment_method : 'Transfer Bank' }}</td>
                    <td style="width: 35%;"><strong>Status Bayar:</strong> 
                        <span class="badge {{ ($order->payment && $order->payment->status === 'confirmed') ? 'badge-success' : 'badge-warning' }}">
                            {{ $order->payment ? strtoupper($order->payment->status) : 'PENDING' }}
                        </span>
                    </td>
                    <td style="width: 35%; text-align: right; font-size: 11px; color: #666;">
                        Dicetak otomatis via TokoKita pada {{ date('d M Y H:i:s') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
