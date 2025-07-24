<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Voucher;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VoucherDetailController extends Controller
{
    public function getVoucherDetail($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $totalPoints = DB::table('user_wallets')
                ->where('user_id', $user->id)
                ->value('total_points');

            $voucher = Voucher::find($id);

            if (!$voucher) {
                return response()->json([
                    'message' => 'Voucher Not Found'
                ], 404);
            }

            $data = [
                'id' => $voucher->id,
                'amount' => $voucher->amount,
                'required_points' => $voucher->required_points,
                'user_points' => $totalPoints ?? 0,
                'button' => ($totalPoints >= $voucher->required_points) ? 'Redeem Cash' : 'Insufficient Points'
            ];

            return response()->json([
                'message' => 'Voucher Fetched Successfully',
                'data' => $data
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

            $totalPoints = DB::table('user_wallets')
                ->where('user_id', $user->id)
                ->value('total_points');


            $vouchers = Voucher::all();

            $data = $vouchers->map(function ($voucher) use ($totalPoints) {
                return [
                    'id' => $voucher->id,
                    'amount' => $voucher->amount,
                    'required_points' => $voucher->required_points,
                    'user_points' => $totalPoints ?? 0,
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
}