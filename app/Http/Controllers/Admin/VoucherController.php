<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    //

    public function index() {
        $vouchers = Voucher::all();
        return view('admin.voucher.index' , compact('vouchers'));
    }

    public function create()
{
    return view('admin.voucher.create');
}


public function store(Request $request)
{
    $request->validate([
        'points' => 'required|integer|unique:vouchers,required_points',
        'rupees' => 'required|numeric|unique:vouchers,rupees'
    ]);

    Voucher::create([
        'required_points' => $request->points,
        'rupees'          => $request->rupees,
        'coupon_code'     => rand(100000, 999999) 
    ]);

    return redirect()->route('voucher.index')->with('success', 'Voucher created successfully');
}


public function edit($id)
{
    $voucher = Voucher::findOrFail($id);
    return view('admin.voucher.edit', compact('voucher'));
}


public function update(Request $request, $id)
{
    $request->validate([
        'points' => 'required|integer|unique:vouchers,required_points,' . $id,
        'rupees' => 'required|numeric'
    ]);

    $voucher = Voucher::findOrFail($id);
    $voucher->update([
        'required_points' => $request->points,
        'rupees'          => $request->rupees,
        'coupon_code'     => rand(100000, 999999) // har update pe naya code
    ]);

    return redirect()->route('voucher.index')->with('success', 'Voucher updated successfully');
}




public function destroy($id)
{
    $voucher = Voucher::findOrFail($id);
    $voucher->delete();

    return redirect()->route('voucher.index')->with('success', 'Voucher deleted successfully');

}

}
