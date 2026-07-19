<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerVoucherController extends Controller
{
    private function getStore()
    {
        return Auth::user()->store ?: abort(404, 'Toko Anda belum terdaftar.');
    }

    public function index()
    {
        $store = $this->getStore();
        $vouchers = Voucher::where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('seller.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('seller.vouchers.create');
    }

    public function store(StoreVoucherRequest $request)
    {
        $store = $this->getStore();

        Voucher::create([
            'store_id' => $store->id,
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'active' => $request->active,
        ]);

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher belanja berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $store = $this->getStore();
        $voucher = Voucher::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        return view('seller.vouchers.edit', compact('voucher'));
    }

    public function update(UpdateVoucherRequest $request, $id)
    {
        $store = $this->getStore();
        $voucher = Voucher::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        $voucher->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'active' => $request->active,
        ]);

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher belanja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $store = $this->getStore();
        $voucher = Voucher::where('store_id', $store->id)->where('id', $id)->firstOrFail();
        $voucher->delete();

        return redirect()->route('seller.vouchers.index')->with('success', 'Voucher belanja berhasil dihapus.');
    }
}
