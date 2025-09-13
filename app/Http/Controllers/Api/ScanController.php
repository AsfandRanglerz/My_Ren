<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Scan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
public function storeScanCode(Request $request)
{
    try {
        $userId = Auth::id();

        // 1️⃣ Check duplicate scan code
       $existingSale = Sale::where('scan_code', $request->scan_code)
    ->where('user_id', $userId) // only check current user
    ->first();

if ($existingSale) {
    return response()->json([
        'status' => false,
        'message' => 'This SN Code already exists.'
    ], 400);
}

       
        // 2️⃣  find product_id from product_batches table
        $productBatch = DB::table('product_batches')
            ->where('scan_code', $request->scan_code)
            ->first();

        if (!$productBatch) {
            return response()->json([
                'status'  => false,
                'message' => 'This SN Code does not exist against any product.'
            ], 400);
        }

        // 3️⃣  get product with its points_per_sale
        $product = Product::find($productBatch->product_id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // 4️⃣  create sale record
        $sale = Sale::create([
            'user_id'       => $userId,
            'product_id'    => $product->id,
            'scan_code'     => $request->scan_code,
            'points_earned' => $product->points_per_sale,
        ]);

        // 🔹 Product points to be added every time
        $pointsToAdd = $product->points_per_sale;

        // 4️⃣ Count total sales by user
        $totalSales = Sale::where('user_id', $userId)->count();

        // 5️⃣ Check install_reward milestone
        $installReward = DB::table('install_rewards')
            ->whereRaw('CAST(target_sales AS UNSIGNED) = ?', [$totalSales])
            ->first();

        if ($installReward) {
            // add milestone points also
            $pointsToAdd += $installReward->points;
        }

        // 6️⃣ Update/Create Wallet with total points
        $wallet = DB::table('user_wallets')
            ->where('user_id', $userId)
            ->first();

        if ($wallet) {
            // update existing wallet
            DB::table('user_wallets')
                ->where('user_id', $userId)
                ->increment('total_points', $pointsToAdd);
        } else {
            // create wallet record if not exist
            DB::table('user_wallets')->insert([
                'user_id'      => $userId,
                'total_points' => $pointsToAdd,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Sale recorded successfully.',
            'sale'    => $sale,
            'added_points' => $pointsToAdd,
            'milestone_reward' => $installReward ? $installReward->points : 0
        ], 200);

    } catch (Exception $e) {
        Log::error('SN Code Save Error:', ['error' => $e->getMessage()]);
        return response()->json([
            'error'   => $e->getMessage(),
            'message' => 'An Error Occurred While Saving the SN Code'
        ], 500);
    }
}


}