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
                'message' => 'Scan Code already exists.'
            ], 400);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $sale = Sale::create([
            'user_id' => $user,
            'product_id' => $request->product_id,
            'scan_code' => $request->scan_code,
            'points_earned' => $product->points_per_sale,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sale recorded successfully.',
            'data' => $sale
        ], 201);

    } catch (Exception $e) {
        Log::error('Scan Code Save Error:', ['error' => $e->getMessage()]);
        return response()->json([
            'error' => $e->getMessage(),
            'message' => 'An Error Occurred While Saving the Scan Code'
        ], 500);
    }
}

}