<?php

namespace App\Http\Controllers\Api;

use Exception;
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
        // $request->validate([
        //     'product_id' => 'required|exists:products,id',
        //     'scan_code' => 'required|string|max:255',
        // ]);
        $user = Auth::id();
        $scan = Scan::create([
            'user_id' => $user,
            'product_id' => $request->product_id,
            'scan_code' => $request->scan_code,
        ]);

        return response()->json([
            'message' => 'Scan code saved successfully',
            'data' => $scan,
        ], 200);

    // } catch (\Illuminate\Validation\ValidationException $e) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Validation failed',
    //         'errors' => $e->errors()
    //     ], 422);

    } catch (Exception $e) {
        Log::error('Scan code save error:', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An error occurred while saving the scan code'
        ], 500);
    }
}
}
