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
        'amount' => 'required|numeric'
    ]);

    Voucher::create([
        'required_points' => $request->points,
        'amount' => $request->amount
    ]);

    return redirect()->route('vouchers.index')->with('success', 'Voucher created successfully!');
}

public function destroy($id)
{
    $voucher = Voucher::findOrFail($id);
    $voucher->delete();

    return redirect()->route('vouchers.index')->with('success', 'Voucher deleted successfully!');

}

}
