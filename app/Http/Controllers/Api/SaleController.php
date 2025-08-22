<?php



namespace App\Http\Controllers\Api;



use App\Models\Sale;

use App\Models\UserWallet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



class SaleController extends Controller

{

    //



      public function store(Request $request)

    {

       
        if(Sale::where('scan_code', $request->scan_code)->exists()) {
            return response()->json(['message' => 'Scan code already exists'], 400);
        }



        // Create a new sale record

        Sale::create([

            'user_id' => $request->user_id, 

            'product_id' => $request->product_id,

            'scan_code' => $request->scan_code,

            'points_earned' => $request->points_earned,

        ]);

         // ✅ Update UserWallet total_points
    $wallet = UserWallet::where('user_id', $request->user_id)->first();

    if ($wallet) {
        // Agar wallet already hai to usme points add karo
        $wallet->total_points += $request->points_earned;
        $wallet->save();
    } else {
        // Agar wallet nahi hai to naya wallet create karo
        UserWallet::create([
            'user_id' => $request->user_id,
            'total_points' => $request->points_earned,
        ]);
    }



       return response()->json([

    'message' => 'Sale recorded successfully'

], 201);



    }


    public function getAllProducts()
{
    try {
        $products = \App\Models\Product::all();

        return response()->json([
            'message' => 'Products retrieved successfully',
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

