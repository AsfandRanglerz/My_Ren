<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class SalesController extends Controller
{
    //

    public function index()
    {

        // Fetch sales data from the database

        $sales = Sale::with('user', 'product')
            ->orderBy('id', 'desc')
            ->get();

        // Return the view with sales data

        return view('admin.sales.index', compact('sales'));

    }
}
