<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishlistRequest;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the wishlisted products.
     */
    public function index()
    {
        $wishlists = Wishlist::with('product.store')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('buyer.wishlist.index', compact('wishlists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWishlistRequest $request)
    {
        $userId = Auth::id();
        $productId = $request->product_id;

        // Prevent duplicate wishlist entry
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Produk ini sudah ada di daftar keinginan Anda.');
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan ke daftar keinginan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        return back()->with('success', 'Produk berhasil dihapus dari daftar keinginan.');
    }
}
