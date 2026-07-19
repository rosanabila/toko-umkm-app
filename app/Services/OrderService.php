<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Process checkout and place an order.
     *
     * @param int $buyerId
     * @param array $data
     * @param int|null $voucherId
     * @param float $voucherDiscount
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function placeOrder(int $buyerId, array $data, ?int $voucherId, float $voucherDiscount)
    {
        $storeId = $data['store_id'];

        return DB::transaction(function () use ($buyerId, $data, $storeId, $voucherId, $voucherDiscount) {
            // Fetch cart items belonging to this store
            $cartItems = Cart::with(['product.store', 'variant'])
                ->where('user_id', $buyerId)
                ->whereHas('product', function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Barang belanjaan tidak ditemukan.');
            }

            // Double check stock before order creation
            foreach ($cartItems as $item) {
                if ($item->product_variant_id) {
                    if ($item->variant->stock < $item->quantity) {
                        throw new \Exception('Stok varian produk "' . $item->product->name . ' - ' . $item->variant->name . '" tidak mencukupi.');
                    }
                } else {
                    if ($item->product->stock < $item->quantity) {
                        throw new \Exception('Stok produk "' . $item->product->name . '" tidak mencukupi.');
                    }
                }
            }

            // Calculations
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->subtotal;
            }

            $shippingCost = $data['shipping_cost'];
            $finalAmount = max(0, $subtotal - $voucherDiscount) + $shippingCost;

            // Generate Order Number: ORD-YYYYMMDD-XXXXX
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Estimate Delivery times based on courier
            $estimate = '2-3 Hari Kerja';
            if (Str::contains($data['shipping_courier'], 'Express') || Str::contains($data['shipping_courier'], 'GoSend')) {
                $estimate = '1 Hari Kerja';
            }

            // 1. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyerId,
                'store_id' => $storeId,
                'voucher_id' => $voucherId ?: null,
                'total_amount' => $subtotal,
                'discount_amount' => $voucherDiscount,
                'shipping_cost' => $shippingCost,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'shipping_recipient_name' => $data['shipping_recipient_name'],
                'shipping_recipient_phone' => $data['shipping_recipient_phone'],
                'shipping_courier' => $data['shipping_courier'],
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

            return $order;
        });
    }
}
