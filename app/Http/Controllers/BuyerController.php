<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\OrderHistory;
use App\Models\OrderReturn;
use App\Models\Review;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuyerController extends Controller
{
    public function orders()
    {
        $orders = Order::with(['store', 'items.product'])
            ->where('buyer_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('buyer.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = Order::with(['store', 'items.product.reviews', 'payment', 'histories', 'returns'])
            ->where('buyer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('buyer.order_detail', compact('order'));
    }

    public function submitPayment(Request $request, $id)
    {
        $order = Order::where('buyer_id', Auth::id())->where('id', $id)->firstOrFail();
        
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'payment_receipt' => 'required|image|max:2048', // Max 2MB image
        ]);

        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            $payment = new Payment(['order_id' => $order->id]);
        }

        // Upload Receipt
        if ($request->hasFile('payment_receipt')) {
            $file = $request->file('payment_receipt');
            $filename = 'receipt_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store receipt in public/uploads/receipts folder
            $file->move(public_path('uploads/receipts'), $filename);
            
            $payment->payment_receipt = 'uploads/receipts/' . $filename;
        }

        $payment->payment_method = $request->payment_method;
        $payment->amount = $order->final_amount;
        $payment->status = 'pending'; // Reset status to pending approval
        $payment->save();

        // Update Order history
        OrderHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'notes' => 'Pembeli mengunggah bukti pembayaran via ' . $request->payment_method . '. Menunggu verifikasi penjual.',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan memverifikasi pembayaran Anda.');
    }

    public function cancelOrder($id)
    {
        $order = Order::with('items')->where('buyer_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan berstatus PENDING yang dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Restore Product stock
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }

            // Update order status
            $order->status = 'cancelled';
            $order->save();

            // Update payment status if any
            if ($order->payment) {
                $order->payment->status = 'failed';
                $order->payment->save();
            }

            // Log history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'notes' => 'Pesanan dibatalkan oleh pembeli. Stok dikembalikan ke sistem.',
            ]);

            DB::commit();
            return back()->with('success', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    public function submitReturn(Request $request, $id)
    {
        $order = Order::where('buyer_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($order->status !== 'completed' && $order->status !== 'shipped') {
            return back()->with('error', 'Pengajuan retur hanya dapat dilakukan untuk pesanan yang telah dikirim atau selesai.');
        }

        $request->validate([
            'reason' => 'required|string',
            'evidence_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $evidencePath = null;
            if ($request->hasFile('evidence_image')) {
                $file = $request->file('evidence_image');
                $filename = 'return_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/returns'), $filename);
                $evidencePath = 'uploads/returns/' . $filename;
            }

            // Create Order Return entry
            OrderReturn::create([
                'order_id' => $order->id,
                'reason' => $request->reason,
                'evidence_image' => $evidencePath,
                'status' => 'pending',
            ]);

            // Update order status
            $order->status = 'returned';
            $order->save();

            // Log history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'returned',
                'notes' => 'Pembeli mengajukan retur barang dengan alasan: ' . $request->reason,
            ]);

            DB::commit();
            return back()->with('success', 'Pengajuan retur barang berhasil diajukan! Menunggu keputusan moderasi Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan retur: ' . $e->getMessage());
        }
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $orderItem = OrderItem::with('order')->findOrFail($request->order_item_id);

        // Security check: must be the buyer of this order
        if ($orderItem->order->buyer_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak memberikan ulasan untuk produk ini.');
        }

        // Verify order is completed
        if ($orderItem->order->status !== 'completed') {
            return back()->with('error', 'Ulasan hanya dapat dikirim jika status pesanan telah SELESAI.');
        }

        // Check if review already exists
        $existingReview = Review::where('order_item_id', $orderItem->id)->first();
        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk dalam transaksi ini.');
        }

        Review::create([
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_moderated' => false,
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim! Terima kasih atas feedback Anda.');
    }
}
