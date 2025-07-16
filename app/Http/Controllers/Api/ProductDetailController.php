<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function getProductDetail(){
        try{
              $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
            $products = Product::select('id','name','image','demissions','points_per_sale')->get();
            if(!$products){
                return response()->json([
                   ' message' => 'No Product Found'
                ],404);
                
            }
            return response()->json([
                'message' => 'Products Detail Found Successfully',
                'data' => $products
            ],200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()
            ],500);
        }
    }
}