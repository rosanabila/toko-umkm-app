<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\OrderHistory;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerController extends Controller
{
    private function getStore()
    {
        return Auth::user()->store ?: abort(404, 'Toko Anda belum terdaftar.');
    }

    public function dashboard()
    {
        $store = $this->getStore();

        // 1. KPI Counts
        // Omzet (Completed or Shipped orders)
        $omzet = Order::where('store_id', $store->id)
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->sum('final_amount');

        $totalOrders = Order::where('store_id', $store->id)->count();
        $totalProducts = Product::where('store_id', $store->id)->count();

        // Pending payments to confirm
        $pendingPaymentsCount = Order::where('store_id', $store->id)
            ->where('status', 'pending')
            ->whereHas('payment', function ($q) {
                $q->whereNotNull('payment_receipt')->where('status', 'pending');
            })->count();

        // Recent Orders
        $recentOrders = Order::with('buyer')
            ->where('store_id', $store->id)
            ->latest()
            ->limit(5)
            ->get();

        // 2. Bagan Status Pesanan (Flow Counts)
        $statusCounts = [
            'pending' => Order::where('store_id', $store->id)->where('status', 'pending')->count(),
            'processing' => Order::where('store_id', $store->id)->where('status', 'processing')->count(),
            'shipped' => Order::where('store_id', $store->id)->where('status', 'shipped')->count(),
            'completed' => Order::where('store_id', $store->id)->where('status', 'completed')->count(),
            'returned' => Order::where('store_id', $store->id)->where('status', 'returned')->count(),
            'cancelled' => Order::where('store_id', $store->id)->where('status', 'cancelled')->count(),
        ];

        return view('seller.dashboard', compact('store', 'omzet', 'totalOrders', 'totalProducts', 'pendingPaymentsCount', 'recentOrders', 'statusCounts'));
    }

    public function profile()
    {
        $store = $this->getStore();
        return view('seller.profile', compact('store'));
    }

    public function profileUpdate(Request $request)
    {
        $store = $this->getStore();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'operating_hours_open' => 'required|date_format:H:i',
            'operating_hours_close' => 'required|date_format:H:i',
            'logo' => 'nullable|image|max:1024', // max 1MB
            'shipping_areas' => 'nullable|string', // raw text or JSON list
        ]);

        // Upload Logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . $store->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logos'), $filename);
            $store->logo = 'uploads/logos/' . $filename;
        }

        // Parse shipping areas (convert comma-separated values to array)
        $areas = [];
        if ($request->filled('shipping_areas')) {
            $areas = array_map('trim', explode(',', $request->shipping_areas));
        }

        $store->name = $request->name;
        $store->description = $request->description;
        $store->address = $request->address;
        $store->phone = $request->phone;
        $store->operating_hours_open = $request->operating_hours_open;
        $store->operating_hours_close = $request->operating_hours_close;
        $store->shipping_areas = $areas;
        $store->save();

        return redirect()->route('seller.profile')->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function orders()
    {
        $store = $this->getStore();
        
        $orders = Order::with('buyer')
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('seller.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $store = $this->getStore();

        $order = Order::with(['buyer', 'items.product', 'payment', 'histories', 'returns'])
            ->where('store_id', $store->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('seller.order_detail', compact('order'));
    }

    public function confirmPayment(Request $request, $id)
    {
        $store = $this->getStore();
        $order = Order::with('payment')->where('store_id', $store->id)->where('id', $id)->firstOrFail();

        if ($order->status !== 'pending' || !$order->payment || $order->payment->status !== 'pending') {
            return back()->with('error', 'Status pembayaran tidak dapat diverifikasi.');
        }

        DB::beginTransaction();
        try {
            // Confirm payment
            $order->payment->status = 'confirmed';
            $order->payment->confirmed_at = Carbon::now();
            $order->payment->confirmed_by = Auth::id();
            $order->payment->save();

            // Set order status to processing
            $order->status = 'processing';
            $order->save();

            // Log history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'processing',
                'notes' => 'Pembayaran terkonfirmasi oleh penjual. Pesanan sedang diproses.',
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil dikonfirmasi! Silakan siapkan pengiriman barang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses konfirmasi: ' . $e->getMessage());
        }
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $store = $this->getStore();
        $order = Order::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        $request->validate([
            'status' => 'required|in:processing,shipped,completed,cancelled',
            'tracking_number' => 'required_if:status,shipped|nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $newStatus = $request->status;

        // Validation for illegal transitions
        if ($newStatus === 'shipped' && $order->status !== 'processing') {
            return back()->with('error', 'Pengiriman hanya bisa diproses untuk pesanan yang sedang diproses (processing).');
        }

        DB::beginTransaction();
        try {
            $historyNotes = $request->input('notes') ?: 'Status pesanan diperbarui oleh penjual.';

            if ($newStatus === 'shipped') {
                $order->tracking_number = $request->tracking_number;
                $historyNotes = 'Pesanan dikirim melalui ' . $order->shipping_courier . ' dengan Nomor Resi: ' . $request->tracking_number . '. ' . ($request->input('notes') ?? '');
            }

            $order->status = $newStatus;
            $order->save();

            // Log history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => $historyNotes,
            ]);

            DB::commit();
            return back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . strtoupper($newStatus));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        $store = $this->getStore();

        // 1. Tren Penjualan per Periode (Last 30 Days)
        $salesTrend = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(final_amount) as total_sales'))
            ->where('store_id', $store->id)
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Produk Terlaris (Top 5)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function ($q) use ($store) {
                $q->where('store_id', $store->id)->whereIn('status', ['processing', 'shipped', 'completed']);
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product')
            ->limit(5)
            ->get();

        // 3. Status Pesanan (flow)
        $orderFlow = Order::select('status', DB::raw('COUNT(*) as count'))
            ->where('store_id', $store->id)
            ->groupBy('status')
            ->get();

        // 4. Analisis Rating & Ulasan (1 to 5 stars)
        $ratings = Review::select('rating', DB::raw('COUNT(*) as count'))
            ->whereHas('product', function ($q) use ($store) {
                $q->where('store_id', $store->id);
            })
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->get();

        return view('seller.reports', compact('store', 'salesTrend', 'topProducts', 'orderFlow', 'ratings'));
    }
}
