<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with(['product.store', 'variant'])
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->subtotal;
        }

        // Handle Voucher Code in Cart View
        $voucher = null;
        $discount = 0;
        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->where('active', true)
                ->first();

            if ($voucher) {
                // If it is a store-specific voucher, verify if any item in cart is from that store
                if ($voucher->store_id) {
                    $hasStoreItem = $cartItems->contains(function ($item) use ($voucher) {
                        return $item->product->store_id === $voucher->store_id;
                    });
                    
                    if ($hasStoreItem) {
                        $storeSubtotal = $cartItems->filter(function ($item) use ($voucher) {
                            return $item->product->store_id === $voucher->store_id;
                        })->sum(function ($item) {
                            return $item->subtotal;
                        });
                        
                        if ($voucher->isValidFor($storeSubtotal)) {
                            $discount = $voucher->calculateDiscount($storeSubtotal);
                            session(['applied_voucher_id' => $voucher->id, 'voucher_discount' => $discount, 'voucher_code' => $voucher->code]);
                        } else {
                            session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);
                            return back()->with('error', 'Voucher toko tidak memenuhi minimum belanja Rp ' . number_format($voucher->min_spend, 0, ',', '.'));
                        }
                    } else {
                        session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);
                        return back()->with('error', 'Voucher ini hanya berlaku untuk pembelian produk dari toko tertentu.');
                    }
                } else {
                    // Global Voucher
                    if ($voucher->isValidFor($subtotal)) {
                        $discount = $voucher->calculateDiscount($subtotal);
                        session(['applied_voucher_id' => $voucher->id, 'voucher_discount' => $discount, 'voucher_code' => $voucher->code]);
                    } else {
                        session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);
                        return back()->with('error', 'Total belanja tidak memenuhi minimum syarat voucher Rp ' . number_format($voucher->min_spend, 0, ',', '.'));
                    }
                }
            } else {
                session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);
                return back()->with('error', 'Kode voucher tidak valid atau telah kedaluwarsa.');
            }
        } else {
            // Retrieve session voucher if exists
            $discount = session('voucher_discount', 0);
        }

        $finalTotal = max(0, $subtotal - $discount);

        return view('cart.index', compact('cartItems', 'subtotal', 'discount', 'finalTotal'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity', 1);

        // Verify Stock
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->firstOrFail();
            if ($variant->stock < $quantity) {
                return back()->with('error', 'Stok varian ini tidak mencukupi.');
            }
        } else {
            if ($product->stock < $quantity) {
                return back()->with('error', 'Stok produk ini tidak mencukupi.');
            }
        }

        // Check if item already exists in buyer's cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            $cartItem->delete();
            return back()->with('success', 'Barang dihapus dari keranjang.');
        }

        // Validate Stock limits
        if ($cartItem->product_variant_id) {
            if ($cartItem->variant->stock < $quantity) {
                return back()->with('error', 'Stok varian tidak mencukupi untuk jumlah tersebut.');
            }
        } else {
            if ($cartItem->product->stock < $quantity) {
                return back()->with('error', 'Stok produk tidak mencukupi untuk jumlah tersebut.');
            }
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        // Recalculate voucher discount if any
        if (session('applied_voucher_id')) {
            session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);
        }

        return back()->with('success', 'Jumlah barang berhasil diperbarui.');
    }

    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        // Clear voucher if cart changes
        session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);

        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    public function checkout()
    {
        $cartItems = Cart::with(['product.store', 'variant'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // TokoKita enforces checkouts grouped per-store (e-commerce best practice)
        // If there are multiple stores in the cart, we group checkouts or notify.
        // For simplicity in this application, we group by store. The buyer checks out all items from ONE store at a time.
        // We will process checkouts for the FIRST store represented in the cart, or notify them.
        $storeId = $cartItems->first()->product->store_id;
        $storeItems = $cartItems->filter(function ($item) use ($storeId) {
            return $item->product->store_id === $storeId;
        });

        $store = $storeItems->first()->product->store;

        $subtotal = 0;
        foreach ($storeItems as $item) {
            $subtotal += $item->subtotal;
        }

        $discount = 0;
        $voucherId = session('applied_voucher_id');
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher) {
                if ($voucher->store_id == null || $voucher->store_id == $storeId) {
                    $discount = session('voucher_discount', 0);
                }
            }
        }

        // If voucher is no longer valid, clear it
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return view('cart.checkout', compact('storeItems', 'store', 'subtotal', 'discount', 'voucherId'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'shipping_address' => 'required|string',
            'shipping_recipient_name' => 'required|string|max:255',
            'shipping_recipient_phone' => 'required|string|max:20',
            'shipping_courier' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $storeId = $request->store_id;
        
        // Fetch cart items belonging to this store
        $cartItems = Cart::with(['product.store', 'variant'])
            ->where('user_id', Auth::id())
            ->whereHas('product', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Barang belanjaan tidak ditemukan.');
        }

        // Double check stock before order creation
        foreach ($cartItems as $item) {
            if ($item->product_variant_id) {
                if ($item->variant->stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Stok varian produk "' . $item->product->name . ' - ' . $item->variant->name . '" tidak mencukupi.');
                }
            } else {
                if ($item->product->stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Stok produk "' . $item->product->name . '" tidak mencukupi.');
                }
            }
        }

        // Calculations
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->subtotal;
        }

        // Verify discount voucher
        $discount = 0;
        $voucherId = session('applied_voucher_id');
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher && ($voucher->store_id == null || $voucher->store_id == $storeId)) {
                $discount = session('voucher_discount', 0);
            }
        }

        $shippingCost = $request->shipping_cost;
        $finalAmount = max(0, $subtotal - $discount) + $shippingCost;

        // Generate Order Number: ORD-YYYYMMDD-XXXXX
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Estimate Delivery times based on courier
        $estimate = '2-3 Hari Kerja';
        if (Str::contains($request->shipping_courier, 'Express') || Str::contains($request->shipping_courier, 'GoSend')) {
            $estimate = '1 Hari Kerja';
        }

        DB::beginTransaction();
        try {
            // 1. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => Auth::id(),
                'store_id' => $storeId,
                'voucher_id' => $voucherId ?: null,
                'total_amount' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'notes' => $request->input('notes'),
                'shipping_address' => $request->shipping_address,
                'shipping_recipient_name' => $request->shipping_recipient_name,
                'shipping_recipient_phone' => $request->shipping_recipient_phone,
                'shipping_courier' => $request->shipping_courier,
                'shipping_estimate' => $estimate,
            ]);

            // 2. Create Order Items and Deduct Stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'discount_amount' => $item->product->price - $item->product->discounted_price,
                ]);

                // Deduct stock
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    $variant->stock -= $item->quantity;
                    $variant->save();
                } else {
                    $product = Product::find($item->product_id);
                    $product->stock -= $item->quantity;
                    $product->save();
                }

                // Delete cart item
                $item->delete();
            }

            // 3. Create initial Payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'Transfer Bank (Verifikasi Manual)',
                'amount' => $order->final_amount,
                'status' => 'pending',
            ]);

            // 4. Log order history
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'notes' => 'Pesanan berhasil dibuat, menanti pembayaran oleh pembeli.',
            ]);

            DB::commit();

            // Clear applied voucher sessions
            session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);

            return redirect()->route('buyer.orderDetail', $order->id)->with('success', 'Pesanan Anda berhasil dibuat! Silakan unggah bukti transfer pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage());
        }
    }
}
