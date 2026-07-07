<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Produk {{ $store->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
        .header {
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .store-info {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .stock-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 8px;
            text-align: left;
            font-weight: 600;
        }
        .stock-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .stock-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-danger { background: #fee2e2; color: #ef4444; }
        .badge-warning { background: #fef9c3; color: #d97706; }
        .badge-success { background: #d1fae5; color: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="title">Laporan Stok Produk Aktif</div>
                        <div class="store-info">
                            <strong>Nama Toko:</strong> {{ $store->name }}<br>
                            <strong>Alamat:</strong> {{ $store->address }}
                        </div>
                    </td>
                    <td style="text-align: right; vertical-align: top; font-size: 11px; color: #666;">
                        <strong>Tanggal Cetak:</strong> {{ date('d M Y') }}<br>
                        <strong>Total Produk:</strong> {{ $products->count() }} item
                    </td>
                </tr>
            </table>
        </div>

        <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 15px;">

        <!-- Table -->
        <table class="stock-table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga Base</th>
                    <th class="text-center">Diskon</th>
                    <th class="text-right">Harga Final</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->variants->isNotEmpty())
                            <div style="font-size: 10px; color: #666; margin-top: 4px;">
                                Varian:
                                @foreach($product->variants as $variant)
                                    {{ $variant->name }} (Stok: {{ $variant->stock }}){{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $product->discount_percent > 0 ? $product->discount_percent . '%' : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($product->variants->isNotEmpty())
                            {{ $product->variants->sum('stock') }} (Varian)
                        @else
                            {{ $product->stock }}
                        </td>
                        @endif
                    <td class="text-center">
                        @php
                            $totalStock = $product->variants->isNotEmpty() ? $product->variants->sum('stock') : $product->stock;
                        @endphp
                        @if($totalStock === 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($totalStock < 10)
                            <span class="badge badge-warning">Kritis</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures / Footer -->
        <div style="margin-top: 40px; text-align: right; font-size: 10px; color: #999;">
            Laporan stok barang dicetak otomatis melalui sistem TokoKita.
        </div>
    </div>
</body>
</html>
