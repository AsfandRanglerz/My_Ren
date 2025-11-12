<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PointDeductionHistory;
use App\Models\TempPointDeductionHistory;

class RedeemptionController extends Controller
{
    //

public function index()
{
    $deductions = PointDeductionHistory::with('users')
        ->where('status', 'allowed')
        ->orderBy('id', 'desc')
        ->get();

    // ✅ Format date_time for each record
    foreach ($deductions as $item) {
        $item->date_time = $item->date_time
            ? date('d-m-Y  g:i a', strtotime($item->date_time))
            : null;
    }

    return view('admin.redeemption.index', compact('deductions'));
}

public function show()
{
    // fetch as collection but store in $deduction (so view's $deduction exists)
    $deduction = TempPointDeductionHistory::with('users')
        ->where('status', 'pending')
		->orWhere('status', 'pending_later')
        ->orderBy('id', 'desc')
        ->get();

    // format date_time for each record
    foreach ($deduction as $item) {
        // if date_time can be null/empty, guard against it
        $item->date_time = $item->date_time
            ? date('d-m-Y  g:i a', strtotime($item->date_time))
            : null;
    }

    // pass $deduction to the view (same name the view expects)
    return view('admin.redeemption.show', compact('deduction'));
}


}
