<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $products = Product::with(['store', 'categories', 'reviews'])
            ->filter($request->only(['search', 'category', 'price_min', 'price_max']))
            ->latest()
            ->paginate(12);

        return view('welcome', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['store', 'categories', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $reviews = $product->reviews()->where('is_moderated', false)->latest()->get();
        
        // Find related products sharing the same categories
        $relatedProducts = Product::whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('product.show', compact('product', 'reviews', 'relatedProducts'));
    }

    public function storeShow($slug)
    {
        $store = Store::with(['products.categories', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $products = $store->products()->latest()->paginate(12);

        return view('store.show', compact('store', 'products'));
    }
}
