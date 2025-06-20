<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Http\Controllers\Controller;

class WithdrawRequestController extends Controller
{
    public function index()
    {
        $withdrawRequests = WithdrawRequest::all();
        return view('admin.withdrawrequest.index', compact('withdrawRequests'));
    }
}
