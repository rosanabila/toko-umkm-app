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
        
        $query = Product::query()->with(['store', 'category', 'reviews']);

        // Search Filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price Min Filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // Price Max Filter
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->latest()->paginate(12);

        return view('welcome', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['store', 'category', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $reviews = $product->reviews()->where('is_moderated', false)->latest()->get();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('product.show', compact('product', 'reviews', 'relatedProducts'));
    }

    public function storeShow($slug)
    {
        $store = Store::with(['products.category', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $products = $store->products()->latest()->paginate(12);

        return view('store.show', compact('store', 'products'));
    }
}
