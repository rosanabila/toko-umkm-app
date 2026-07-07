<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\Review;
use App\Models\OrderReturn;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. System-wide KPI
        $totalRevenue = Order::whereIn('status', ['processing', 'shipped', 'completed'])->sum('final_amount');
        $totalUsers = User::count();
        $totalStores = Store::count();
        $totalOrders = Order::count();

        // 2. Performa Penjual (Revenue comparison between stores)
        $sellerPerformance = Store::select('stores.id', 'stores.name', DB::raw('SUM(orders.final_amount) as total_sales'))
            ->leftJoin('orders', 'stores.id', '=', 'orders.store_id')
            ->whereIn('orders.status', ['processing', 'shipped', 'completed'])
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // 3. Bagan Status Pesanan (Global)
        $orderFlow = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('admin.dashboard', compact('totalRevenue', 'totalUsers', 'totalStores', 'totalOrders', 'sellerPerformance', 'orderFlow'));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent editing self role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $request->validate([
            'role' => 'required|in:admin,penjual,pembeli',
        ]);

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        // If updated to penjual and store doesn't exist, create store profile template
        if ($user->role === 'penjual' && !$user->store) {
            $user->store()->create([
                'name' => 'Toko Baru ' . $user->name,
                'slug' => 'toko-baru-' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $user->name)) . '-' . rand(100, 999),
                'description' => 'Profil toko baru. Hubungi pemilik untuk melakukan konfigurasi.',
            ]);
        }

        return back()->with('success', 'Role pengguna ' . $user->name . ' berhasil diperbarui dari ' . $oldRole . ' menjadi ' . $user->role . '.');
    }

    public function reviews()
    {
        $reviews = Review::with(['product.store', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.reviews', compact('reviews'));
    }

    public function moderateReview(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'is_moderated' => 'required|boolean',
            'moderation_notes' => 'nullable|string',
        ]);

        $review->is_moderated = $request->is_moderated;
        $review->moderated_by = Auth::id();
        $review->moderation_notes = $request->moderation_notes;
        $review->save();

        $actionText = $review->is_moderated ? 'disembunyikan (moderasi aktif)' : 'ditampilkan kembali (moderasi nonaktif)';
        return back()->with('success', 'Ulasan oleh ' . $review->user->name . ' berhasil ' . $actionText . '.');
    }

    public function returns()
    {
        $returns = OrderReturn::with(['order.buyer', 'order.store'])
            ->latest()
            ->paginate(15);

        return view('admin.returns', compact('returns'));
    }

    public function moderateReturn(Request $request, $id)
    {
        $return = OrderReturn::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $return->status = $request->status;
            $return->admin_notes = $request->admin_notes;
            $return->save();

            // Log Order History
            $historyNotes = 'Pengajuan retur barang ' . ($request->status === 'approved' ? 'DISETUJUI' : 'DITOLAK') . ' oleh Administrator. ' . ($request->admin_notes ? 'Catatan: ' . $request->admin_notes : '');
            OrderHistory::create([
                'order_id' => $return->order_id,
                'status' => $return->order->status,
                'notes' => $historyNotes,
            ]);

            DB::commit();
            return back()->with('success', 'Pengajuan retur berhasil dimoderasi menjadi status: ' . strtoupper($request->status));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses moderasi retur: ' . $e->getMessage());
        }
    }
}
