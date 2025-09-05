<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    //
public function getAllProducts()
{
    try {
        $products = \App\Models\Product::all()->map(function ($product) {
            $product->image = 'public/' . $product->image;
            return $product;
        });

        return response()->json([
            'message' => 'Products Fetched successfully',
            'data' => $products
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
