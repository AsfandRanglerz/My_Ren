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
            'message' => 'Sales with product names fetched successfully',
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function store(Request $request)
    {
        try {
           

            SearchHistory::create([
                'user_id' => $request->user_id,
                'product_name' => $request->product_name,
            ]);

            
            return response()->json([
                'message' => 'Saved Successfully'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve all search history for the logged-in user.
     */
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

    /**
     * Delete a specific search history entry.
     */
    public function destroy($id)
    {
        try {
            $history = SearchHistory::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$history) {
                return response()->json([
                    'message' => 'Record Not Found'
                ], 404);
            }

            $history->delete();

            return response()->json([
                'message' => 'Deleted Successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unable To Delete Entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all search history for the logged-in user.
     */
    public function clearAll()
    {
        try {
            SearchHistory::where('user_id', auth()->id())->delete();

            return response()->json([
                'message' => 'All History Deleted Successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unable To Clear History',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function searchUserSalesByProductName(Request $request)
{
    try {
        $userId = Auth::id();

       

        // Step 1: Get matching product IDs
        $sales = Sale::whereHas('product', function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->product_name . '%');
            })
            ->where('user_id', auth()->id()) // filter user-specific data
            ->with('product') // eager load product details
            ->get();


            if (!$sales) {
                        return response()->json([
                            'message' => 'No matching sales found'
                        ], 404);
                    } else{

                        return response()->json($sales, 200);
                    }
       
        

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}
