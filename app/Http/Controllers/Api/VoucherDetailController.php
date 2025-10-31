<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\Voucher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherDetailController extends Controller
{
//     public function getVoucherDetail()
// {
//     try {
//         $user = Auth::user();

//         if (!$user) {
//             return response()->json([
//                 'message' => 'Unauthorized'
//             ], 401);
//         }

//         // User ke points
//         $totalPoints = DB::table('user_wallets')
//             ->where('user_id', $user->id)
//             ->value('total_points');

//         // Sirf required fields select karo
//      $claimedVouchers = DB::table('claim_vouchers')
//             ->join('vouchers', 'claim_vouchers.voucher_id', '=', 'vouchers.id')
//             ->where('claim_vouchers.user_id', $user->id)
//             ->select(
//                 'claim_vouchers.coupon_code',
//                 'vouchers.voucher_code',
//                 'vouchers.rupees',
//             )
//              ->orderBy('claim_vouchers.id', 'desc')
//             ->get();

//         if (!$claimedVouchers) {
//             return response()->json([
//                 'message' => 'Voucher Not Found'
//             ], 404);
//         }
//     return response()->json([
//             'message' => 'Claimed vouchers fetched successfully',
//             'data' => $claimedVouchers,
//             'user_points' => $totalPoints ?? 0
//         ], 200);

//     } catch (Exception $e) {
//         return response()->json([
//             'message' => 'Something Went Wrong',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

public function getVoucherDetail()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // User ke total points
        $totalPoints = DB::table('user_wallets')
            ->where('user_id', $user->id)
            ->value('total_points');

        // Sirf claim_vouchers table se data lo (fresh/top first)
        $claimedVouchers = DB::table('claim_vouchers')
            ->where('user_id', $user->id)
            ->select('coupon_code', 'points')
            ->orderBy('id', 'desc') // latest first
            ->get();

        if ($claimedVouchers->isEmpty()) {
            return response()->json([
                'message' => 'Voucher Not Found'
            ], 404);
        }

        return response()->json([
            'message' => 'Claimed vouchers fetched successfully',
            'data' => $claimedVouchers,
            'user_points' => $totalPoints ?? 0
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Something Went Wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}



   public function getVoucher()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // User total points get karo
        $totalPoints = DB::table('user_wallets')
            ->where('user_id', $user->id)
            ->value('total_points');

        // Sirf required fields select karo
        $vouchers = DB::table('vouchers')
		->select('id', 'required_points', 'rupees', 'voucher_code')
		->orderBy('required_points', 'asc')
		->get();

        // Data format
        $data = $vouchers->map(function ($voucher) use ($totalPoints) {
            return [
                'id'             => $voucher->id,
                'required_points'=> $voucher->required_points,
                'rupees'         => $voucher->rupees,
                'user_points'    => $totalPoints ?? 0,
                'voucher_code'   => $voucher->voucher_code,
            ];
        });

        return response()->json([
            'message' => 'Vouchers Fetched Successfully',
            'data' => $data
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Something Went Wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

      public function ClaimVoucher(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        // User wallet se current points lo
        $wallet = DB::table('user_wallets')->where('user_id', $user->id)->first();

        if (!$wallet || $wallet->total_points < $request->points) {
            return response()->json([
                'message' => 'Not Enough Points to Claim this Voucher'
            ], 422);
        }

        // 4 digit random coupon code
        $couponCode = strtoupper(Str::random(4));

        DB::beginTransaction();

        // 1. Minus points from user wallet
        DB::table('user_wallets')
            ->where('user_id', $user->id)
            ->update([
                'total_points' => $wallet->total_points - $request->points,
                'updated_at'   => now()
            ]);

        // 2. Insert into claim_vouchers
        $id = DB::table('claim_vouchers')->insertGetId([
            'user_id'     => $user->id,
            'points'  => $request->points,
            'coupon_code' => $couponCode,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::commit();

        // 3. Fetch created record
        $data = DB::table('claim_vouchers')->where('id', $id)->first();

        //   // Voucher amount from DB
        //     $voucher = DB::table('vouchers')
        //         ->where('id', $request->voucher_id)
        //         ->select('rupees')
        //         ->first();
        //     $clamingVoucherAmountOff = $voucher->rupees; 
        //     $value = number_format((float) $clamingVoucherAmountOff, 2, '.', ''); 
        //     // Shopify API call via private function
        //     $shopifyResponse = $this->createShopifyDiscount($user->id, $couponCode, $value);
        //     if (isset($shopifyResponse['error'])) {
        //         DB::rollBack();
        //         return response()->json($shopifyResponse, 500);
        //     }
        //     DB::commit();
              return response()->json([
            'message' => 'Voucher Claimed Successfully',
            'data'    => $data,
            // 'shopify' => $shopifyResponse
        ], 200);


    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Something Went Wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}    

  
    // private function createShopifyDiscount($customerId, $couponCode, $discountValue)
    // {
    //     $shopUrl = config('services.shopify.store_url');
    //     $accessToken = config('services.shopify.access_token');
    //     $apiVersion = config('services.shopify.api_version', '2025-01');
    //     // Step 1: Price Rule
    //     $priceRuleData = [
    //         "price_rule" => [
    //             "title" => "Customer-Discount-{$customerId} | {$couponCode}",
    //             "value_type" => "fixed_amount",
    //             "value" => "-" . $discountValue,
    //             "customer_selection" => "all",
    //             "target_type" => "line_item",
    //             "target_selection" => "all",
    //             "allocation_method" => "across",
    //             "usage_limit" => 1,
    //             "once_per_customer" => true,
    //             "starts_at" => now()->toIso8601String()
    //         ]
    //     ];
    //     $ch = curl_init("https://$shopUrl/admin/api/$apiVersion/price_rules.json");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($priceRuleData));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "Content-Type: application/json",
    //         "X-Shopify-Access-Token: $accessToken"
    //     ]);
    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $curlError = curl_error($ch);
    //     curl_close($ch);
    //     $priceRule = json_decode($response, true);
    //     $priceRuleId = $priceRule['price_rule']['id'] ?? null;
    //     if (!$priceRuleId) {
    //         return [
    //             'error' => 'Price Rule creation failed',
    //             'http_code' => $httpCode,
    //             'curl_error' => $curlError,
    //             'shopify_response' => $priceRule
    //         ];
    //     }
    //     // Step 2: Discount Code
    //     $discountData = [
    //         "discount_code" => [
    //             "code" => $couponCode
    //         ]
    //     ];
    //     $ch = curl_init("https://$shopUrl/admin/api/$apiVersion/price_rules/$priceRuleId/discount_codes.json");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discountData));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "Content-Type: application/json",
    //         "X-Shopify-Access-Token: $accessToken"
    //     ]);
    //     $discountResponse = curl_exec($ch);
    //     curl_close($ch);
    //     $discountCode = json_decode($discountResponse, true);
    //     if (!isset($discountCode['discount_code']['id'])) {
    //         return [
    //             'error' => 'Discount Code creation failed',
    //             'shopify_response' => $discountCode
    //         ];
    //     }
    //     return [
    //         'price_rule_id' => $priceRuleId,
    //         'discount_code' => $discountCode['discount_code']
    //     ];
    // }



  
}