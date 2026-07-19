<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\OrderHistory;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
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

        $voucherId = session('applied_voucher_id');
        $voucherDiscount = session('voucher_discount', 0);

        try {
            $order = $this->orderService->placeOrder(
                Auth::id(),
                $request->all(),
                $voucherId,
                $voucherDiscount
            );

            // Clear applied voucher sessions
            session()->forget(['applied_voucher_id', 'voucher_discount', 'voucher_code']);

            return redirect()->route('buyer.orderDetail', $order->id)->with('success', 'Pesanan Anda berhasil dibuat! Silakan unggah bukti transfer pembayaran.');

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage());
        }
    }
}
