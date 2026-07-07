<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerProductController extends Controller
{
    private function getStore()
    {
        return Auth::user()->store ?: abort(404, 'Toko Anda belum terdaftar.');
    }

    public function index()
    {
        $store = $this->getStore();
        $products = Product::with(['categories', 'variants'])
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $store = $this->getStore();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB image
            
            // Variants validation
            'variant_name.*' => 'nullable|string|max:100',
            'variant_price.*' => 'nullable|numeric|min:0',
            'variant_stock.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'prod_' . $store->id . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $filename);
                $imagePath = 'uploads/products/' . $filename;
            }

            // Create base product
            $slug = Str::slug($request->name) . '-' . strtolower(Str::random(4));
            $product = Product::create([
                'store_id' => $store->id,
                'name' => $request->name,
                'slug' => $slug,
                'price' => $request->price,
                'stock' => $request->stock,
                'discount_percent' => $request->discount_percent,
                'description' => $request->description,
                'image' => $imagePath,
            ]);
            $product->categories()->attach($request->category_id);

            // Save variants if provided
            if ($request->filled('variant_name')) {
                $varNames = $request->variant_name;
                $varPrices = $request->variant_price;
                $varStocks = $request->variant_stock;

                for ($i = 0; $i < count($varNames); $i++) {
                    if (empty($varNames[$i])) continue;
                    
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $varNames[$i],
                        'additional_price' => $varPrices[$i] ?? 0.00,
                        'stock' => $varStocks[$i] ?? 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $store = $this->getStore();
        $product = Product::with('variants')
            ->where('store_id', $store->id)
            ->where('id', $id)
            ->firstOrFail();

        $categories = Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $store = $this->getStore();
        $product = Product::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            
            // Variants
            'variant_id.*' => 'nullable|integer',
            'variant_name.*' => 'nullable|string|max:100',
            'variant_price.*' => 'nullable|numeric|min:0',
            'variant_stock.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Image Upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'prod_' . $store->id . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $filename);
                $product->image = 'uploads/products/' . $filename;
            }

            // Update product base fields
            $product->name = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->discount_percent = $request->discount_percent;
            $product->description = $request->description;
            
            // If name changed significantly, regenerate slug (optional, but let's update it to match new name)
            $product->slug = Str::slug($request->name) . '-' . strtolower(Str::random(4));
            $product->save();

            // Sync categories pivot table
            $product->categories()->sync([$request->category_id]);

            // Manage variants
            // 1. Get current variants and details
            $existingVariantIds = $product->variants->pluck('id')->toArray();
            $formVariantIds = $request->variant_id ?: [];

            // 2. Delete variants not in the form
            $toDelete = array_diff($existingVariantIds, $formVariantIds);
            if (!empty($toDelete)) {
                ProductVariant::whereIn('id', $toDelete)->delete();
            }

            // 3. Update or Insert variants
            if ($request->filled('variant_name')) {
                $varIds = $request->variant_id;
                $varNames = $request->variant_name;
                $varPrices = $request->variant_price;
                $varStocks = $request->variant_stock;

                for ($i = 0; $i < count($varNames); $i++) {
                    if (empty($varNames[$i])) continue;

                    $vId = $varIds[$i] ?? null;
                    if ($vId && in_array($vId, $existingVariantIds)) {
                        // Update
                        $variant = ProductVariant::find($vId);
                        $variant->name = $varNames[$i];
                        $variant->additional_price = $varPrices[$i] ?? 0.00;
                        $variant->stock = $varStocks[$i] ?? 0;
                        $variant->save();
                    } else {
                        // Create
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'name' => $varNames[$i],
                            'additional_price' => $varPrices[$i] ?? 0.00,
                            'stock' => $varStocks[$i] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $store = $this->getStore();
        $product = Product::where('store_id', $store->id)->where('id', $id)->firstOrFail();
        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
