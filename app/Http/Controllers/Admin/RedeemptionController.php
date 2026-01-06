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
    // ✅ Temp table records
    $tempDeductions = TempPointDeductionHistory::with('users')
        ->whereIn('status', ['pending', 'pending_later'])
        ->orderBy('id', 'desc')
        ->get();

    // ✅ Main history records (ONLY pending_later)
    $mainDeductions = PointDeductionHistory::with('users')
        ->where('status', 'pending_later')
        ->orderBy('id', 'desc')
        ->get();

    // ✅ Merge both collections
    $deduction = $tempDeductions->merge($mainDeductions);

    // ✅ Sort merged data again (latest first)
    $deduction = $deduction->sortByDesc('id')->values();

    // ✅ Format date_time
    foreach ($deduction as $item) {
        $item->date_time = $item->date_time
            ? date('d-m-Y g:i a', strtotime($item->date_time))
            : null;
    }

    return view('admin.redeemption.show', compact('deduction'));
}



}
