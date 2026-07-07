<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
        .header-table, .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .items-table th {
            background: #eee;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="title">Surat Jalan Pengiriman</div>
                    <div style="font-size: 12px; color: #666;">No. Referensi: {{ $order->order_number }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <strong>Tanggal Kirim:</strong> {{ date('d M Y') }}<br>
                    <strong>Kurir:</strong> {{ $order->shipping_courier }}
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 1px solid #ccc; margin-bottom: 20px;">

        <!-- Sender / Receiver Info -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="section-title">Pengirim (UMKM):</div>
                    <strong>{{ $order->store->name }}</strong><br>
                    Telepon: {{ $order->store->phone ?? $order->store->user->phone }}<br>
                    Alamat: {{ $order->store->address }}
                </td>
                <td>
                    <div class="section-title">Penerima Paket:</div>
                    <strong>{{ $order->shipping_recipient_name }}</strong><br>
                    Telepon: {{ $order->shipping_recipient_phone }}<br>
                    Alamat: {{ $order->shipping_address }}
                </td>
            </tr>
        </table>

        <!-- Items list -->
        <div class="section-title" style="margin-bottom: 8px;">Daftar Barang dalam Paket:</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">No.</th>
                    <th style="width: 70%;">Nama Produk</th>
                    <th style="width: 20%; text-align: center;">Kuantitas</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($order->items as $item)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->variant)
                            <br><span style="font-size: 11px; color: #555;">Varian: {{ $item->variant->name }}</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-size: 15px; font-weight: bold;">{{ $item->quantity }} pcs</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notes -->
        @if($order->notes)
        <div style="margin-top: 15px; background: #fdfdfd; padding: 10px; border: 1px dashed #ccc; border-radius: 4px;">
            <strong>Catatan Pengiriman:</strong><br>
            {{ $order->notes }}
        </div>
        @endif

        <!-- Signatures -->
        <table class="signatures">
            <tr>
                <td>
                    <div>Dibuat Oleh,</div>
                    <div style="margin-top: 60px; font-weight: bold; text-decoration: underline;">{{ $order->store->name }}</div>
                </td>
                <td>
                    <div>Diterima Oleh,</div>
                    <div style="margin-top: 60px; border-bottom: 1px solid #333; width: 150px; margin-left: auto; margin-right: auto; height: 16px;"></div>
                    <div style="margin-top: 5px;">( Nama Terang Penerima )</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
