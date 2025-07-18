<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Sale;
use App\Models\Scan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function storeScanCode(Request $request)
{

    try {
      
        $user = Auth::id();
      


         $existingSale = Sale::where('scan_code', $request->scan_code)->first();

    if ($existingSale) {
        return response()->json([
            'status' => false,
            'message' => 'Scan Code is Already Exist.'
        ], 400);
    }

    // Get product points from products table
    $product = Product::find($request->product_id);

    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'Product not found.'
        ], 404);
    }

    // Create sale
    $sale = Sale::create([
        'user_id' => $user, // Make sure user_id is passed correctly
        'product_id' => $request->product_id,
        'scan_code' => $request->scan_code,
        'points_earned' => $product->points // dynamically from products table
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Sale Recorded Successfully.',
        'data' => $sale
    ], 201);


        return response()->json([
            'message' => 'Scan Code Saved Successfully.',
            'data' => $scan,
        ], 200);


    } catch (Exception $e) {
        Log::error('Scan Code Save Error:', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An Error Occurred While Saving the Scan Code'
        ], 500);
    }
}
}
