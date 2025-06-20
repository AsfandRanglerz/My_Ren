<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    //

      public function store(Request $request)
    {

        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'scan_code' => 'required|string|max:255',
            'points_earned' => 'required|integer|min:0',
        ], [
            'user_id.required' => 'User ID is required.',
            'product_id.required' => 'Product ID is required.',
            'scan_code.required' => 'Code Should be unique.',
            'points_earned.required' => 'Points earned are required.',
            'points_earned.integer' => 'Points earned must be an integer.',
            'points_earned.min' => 'Points earned must be at least 0.',
        ]);

        // Create a new sale record
        Sale::create([
            'user_id' => $request->user_id, 
            'product_id' => $request->product_id,
            'scan_code' => $request->scan_code,
            'points_earned' => $request->points_earned,
        ]);

       return response()->json([
    'message' => 'Sale recorded successfully.'
], 201);

    }
}
