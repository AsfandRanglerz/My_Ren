<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
   public function getUserProductSales()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // user ke sales ke saath product relation ko eager load karein
        $sales = $user->sales()->with('product:id,name,image')->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'message' => 'No devices found for this user'
            ], 404);
        }

        // sales ko map kar ke sirf zaroori data nikalain
        $productSales = $sales->map(function ($sale) {
            return [
                'product_id' => $sale->product_id,
                'product_name' => $sale->product->name ?? '',
                'product_image' => $sale->product->image ?? '',
                'sale_date' => $sale->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'message' => 'Sales found successfully',
            'data' => $productSales
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

}