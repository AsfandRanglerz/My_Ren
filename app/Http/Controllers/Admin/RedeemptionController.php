<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PointDeductionHistory;

class RedeemptionController extends Controller
{
    //

	public function index() {
		$deductions = PointDeductionHistory::with('users')
    ->where('status', 'allowed')
    ->orderBy('id', 'desc')
    ->get();
		return view('admin.redeemption.index', compact('deductions'));

	}
}
