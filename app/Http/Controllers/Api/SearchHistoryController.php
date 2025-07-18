<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Product;
use Exception;

class SearchHistoryController extends Controller
{
    /**
     * Store a new search history entry for the logged-in user.
     */

    public function getUserSoldProductNames()
{
    try {
        $userId = Auth::id();

        // Fetch sales for the logged-in user with product relation
        $sales = Sale::where('user_id', $userId)
                     ->with('product:id,name') // eager load product with only id and name
                     ->get(['id', 'product_id', 'scan_code']);

        // Format response
        $data = $sales->map(function ($sale) {
            return [
                'product_id' => $sale->product_id,
                'product_name' => $sale->product->name ?? 'N/A',
                'scan_code' => $sale->scan_code,
            ];
        });

        return response()->json([
            'message' => 'Sales With Product Names Fetched Successfully',
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something Went Wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function index()
    {
        try {
            $history = SearchHistory::where('user_id', auth()->id())
                ->latest()
                ->get();

            return response()->json([
                'data' => $history
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unable To Fetch History',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  


    public function searchUserSalesByProductName(Request $request)
    {

        // return $request;
    
        try {
            $userId = Auth::id();

        

            // Step 1: Get matching product IDs
            $sales = Sale::whereHas('product', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->product_name . '%');
                })
                ->where('user_id', auth()->id()) // filter user-specific data
                ->with('product') // eager load product details
                ->get();


                if ($sales->isEmpty()) {
                    return response()->json([
                        'message' => 'No Matching Sales Found'
                    ], 404);
                } else {

                            return response()->json($sales, 200);
                        }
        
            

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
