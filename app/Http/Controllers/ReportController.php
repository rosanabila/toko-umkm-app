<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function checkOrderAccess(Order $order)
    {
        $user = Auth::user();
        if ($user->isAdmin()) return;
        if ($user->isPenjual() && $order->store_id === $user->store->id) return;
        if ($user->isPembeli() && $order->buyer_id === $user->id) return;
        
        abort(403, 'Anda tidak memiliki akses ke laporan ini.');
    }

    private function getSellerStore()
    {
        return Auth::user()->store ?: abort(404, 'Toko Anda belum terdaftar.');
    }

    // 1. Invoice Pesanan (PDF)
    public function invoicePdf($id)
    {
        $order = Order::with(['buyer', 'store', 'items.product.category', 'payment'])->findOrFail($id);
        $this->checkOrderAccess($order);

        $pdf = Pdf::loadView('reports.invoice', compact('order'));
        return $pdf->stream('Invoice_' . $order->order_number . '.pdf');
    }

    // 2. Surat Jalan Pengiriman (PDF)
    public function deliveryNotePdf($id)
    {
        $order = Order::with(['buyer', 'store', 'items.product'])->findOrFail($id);
        $this->checkOrderAccess($order);

        $pdf = Pdf::loadView('reports.delivery_note', compact('order'));
        return $pdf->stream('SuratJalan_' . $order->order_number . '.pdf');
    }

    // 3. Laporan Stok Produk (PDF)
    public function stockPdf()
    {
        $store = $this->getSellerStore();
        $products = Product::with('category')->where('store_id', $store->id)->latest()->get();

        $pdf = Pdf::loadView('reports.stock', compact('store', 'products'));
        return $pdf->stream('Laporan_Stok_' . $store->slug . '.pdf');
    }

    // 4. Rekap Penjualan (CSV)
    public function salesCsv(Request $request)
    {
        $store = $this->getSellerStore();

        // Get sales data for the last 90 days
        $sales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as gross_amount'),
                DB::raw('SUM(discount_amount) as total_discounts'),
                DB::raw('SUM(shipping_cost) as total_shipping'),
                DB::raw('SUM(final_amount) as net_amount')
            )
            ->where('store_id', $store->id)
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="Rekap_Penjualan_' . $store->slug . '_' . date('Ymd') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header columns
            fputcsv($file, [
                'Tanggal', 
                'Total Transaksi Selesai', 
                'Bruto Item (IDR)', 
                'Total Diskon (IDR)', 
                'Total Ongkir (IDR)', 
                'Omzet Bersih (IDR)'
            ], ';');

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->date,
                    $sale->total_orders,
                    number_format($sale->gross_amount, 2, ',', ''),
                    number_format($sale->total_discounts, 2, ',', ''),
                    number_format($sale->total_shipping, 2, ',', ''),
                    number_format($sale->net_amount, 2, ',', ''),
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 5. Ekspor Data Pesanan & Pembeli (CSV)
    public function ordersCsv(Request $request)
    {
        $store = $this->getSellerStore();

        // Get details of orders
        $orders = Order::with(['buyer', 'payment'])
            ->where('store_id', $store->id)
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="Data_Pesanan_Pembeli_' . $store->slug . '_' . date('Ymd') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header columns
            fputcsv($file, [
                'No Pesanan', 
                'Tanggal Pesanan', 
                'Nama Pembeli', 
                'Email Pembeli', 
                'Telepon Pembeli', 
                'Alamat Pengiriman', 
                'Kurir', 
                'Status Pesanan', 
                'Metode Pembayaran', 
                'Status Bayar', 
                'Total Pembayaran (IDR)'
            ], ';');

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->buyer->name,
                    $order->buyer->email,
                    $order->buyer->phone,
                    str_replace(["\r", "\n"], ' ', $order->shipping_address),
                    $order->shipping_courier,
                    strtoupper($order->status),
                    $order->payment ? $order->payment->payment_method : '-',
                    $order->payment ? strtoupper($order->payment->status) : 'PENDING',
                    number_format($order->final_amount, 2, ',', ''),
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
