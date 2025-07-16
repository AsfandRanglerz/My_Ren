<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use App\Models\Sale;
use Exception;

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

            $totalPoints = Sale::where('user_id', $user->id)->sum('points_earned');

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
                'user_points' => $totalPoints,
                'button' => $totalPoints >= $voucher->required_points ? 'Redeem Cash' : 'Insufficient Points'
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

            $totalPoints = Sale::where('user_id', $user->id)->sum('points_earned');

            
            $vouchers = Voucher::all();

            $data = $vouchers->map(function ($voucher) use ($totalPoints) {
                return [
                    'id' => $voucher->id,
                    'amount' => $voucher->amount,
                    'required_points' => $voucher->required_points,
                    'user_points' => $totalPoints,
                   
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