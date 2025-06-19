<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    //

    public function index()
    {
        // Fetch sales data from the database
        $sales = Sale::with('user', 'product')->get();

        // Return the view with sales data
        return view('admin.sales.index', compact('sales'));
    }

  
}
