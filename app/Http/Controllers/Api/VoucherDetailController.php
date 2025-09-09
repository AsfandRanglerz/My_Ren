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
    public function getVoucherDetail()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // User ke points
        $totalPoints = DB::table('user_wallets')
            ->where('user_id', $user->id)
            ->value('total_points');

        // Sirf required fields select karo
     $claimedVouchers = DB::table('claim_vouchers')
            ->join('vouchers', 'claim_vouchers.voucher_id', '=', 'vouchers.id')
            ->where('claim_vouchers.user_id', $user->id)
            ->select(
                'claim_vouchers.coupon_code',
                'vouchers.voucher_code',
                'vouchers.rupees',
            )
            ->get();

        if (!$claimedVouchers) {
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

        // Voucher detail lo
        $voucher = Voucher::find($request->voucher_id);

        if (!$voucher) {
            return response()->json([
                'message' => 'Voucher not found'
            ], 404);
        }

        // User wallet se current points lo
        $wallet = DB::table('user_wallets')->where('user_id', $user->id)->first();

        if (!$wallet || $wallet->total_points < $voucher->required_points) {
            return response()->json([
                'message' => 'Not Enough Points to Claim this Voucher'
            ], 400);
        }

        // 4 digit random coupon code
        $couponCode = strtoupper(Str::random(4));

        DB::beginTransaction();

        // 1. Minus points from user wallet
        DB::table('user_wallets')
            ->where('user_id', $user->id)
            ->update([
                'total_points' => $wallet->total_points - $voucher->required_points,
                'updated_at'   => now()
            ]);

        // 2. Insert into claim_vouchers
        $id = DB::table('claim_vouchers')->insertGetId([
            'user_id'     => $user->id,
            'voucher_id'  => $voucher->id,
            'coupon_code' => $couponCode,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::commit();

        // 3. Fetch created record
        $data = DB::table('claim_vouchers')->where('id', $id)->first();

        return response()->json([
            'message' => 'Voucher Claimed Successfully',
            'data'    => $data
        ], 200);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Something Went Wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}


}