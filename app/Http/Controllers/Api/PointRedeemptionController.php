<?php

namespace App\Http\Controllers\Api;

use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PointDeductionHistory;
use App\Models\TempPointDeductionHistory;

class PointRedeemptionController extends Controller
{
    //

	// public function getPendingDeduction(Request $request)
	// {
	// 	try {
	// 		$userId = auth()->id(); // ✅ Logged-in user ID

	// 		if (!$userId) {
	// 			return response()->json([
	// 				'status' => false,
	// 				'message' => 'User not authenticated.',
	// 			], 401);
	// 		}

	// 		// ✅ Get all pending deductions for this user
	// 		$pendings = TempPointDeductionHistory::where('user_id', $userId)->get();

	// 		if ($pendings->isEmpty()) {
	// 			return response()->json([
	// 				'status' => false,
	// 				'message' => 'No pending deduction.',
	// 			], 404);
	// 		}

	// 		// ✅ Calculate total points pending
	// 		$totalPendingPoints = $pendings->sum('deducted_points');

	// 		// Optional: create a combined message for all pending requests
	// 		$adminNames = $pendings->pluck('Admin_name')->unique()->join(', ');
	// 		$message = "There is a Shopkeeper {$adminNames} wants to redeem your points ({$totalPendingPoints}). Do you allow this?";

	// 		return response()->json([
	// 			'status' => true,
	// 			'message' => $message,
	// 			'total_pending_points' => $totalPendingPoints,
	// 			'requests' => $pendings->map(function($item){
	// 				return [
	// 					'request_id' => $item->id,
	// 					'points' => $item->deducted_points,
	// 					'admin_name' => $item->Admin_name,
	// 					'admin_type' => $item->Admin_type,
	// 				];
	// 			}),
	// 		], 200);

	// 	} catch (\Exception $e) {
	// 		return response()->json([
	// 			'status' => false,
	// 			'message' => 'An error occurred while checking for pending deductions.',
	// 			'error' => $e->getMessage(),
	// 		], 500);
	// 	}
	// }

public function getPendingDeduction(Request $request)
{
    try {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // ✅ Get all pending deductions for this user
        $pendings = TempPointDeductionHistory::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pendings->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No pending deduction.',
            ], 404);
        }

        // ✅ Format the response for each pending request
        $requests = $pendings->map(function ($item) {
            return [
                'request_id' => $item->id,
                'points' => $item->deducted_points,
                'admin_name' => $item->Admin_name,
                'admin_type' => $item->Admin_type,
                'message' => "Shopkeeper {$item->Admin_name} wants to redeem your {$item->deducted_points} points. Do you allow this?",
            ];
        });

        return response()->json([
            'status' => true,
            'pending_requests' => $requests,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while checking for pending deductions.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function approveDeduction(Request $request)
{
    $userId = auth()->id();

    try {
        // ✅ Get all pending deduction records for this user
        $deductions = TempPointDeductionHistory::where('user_id', $userId)->get();

        if ($deductions->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No pending deduction request found.'], 404);
        }

        // ✅ Sum total points to deduct
        $totalDeductedPoints = $deductions->sum('deducted_points');

        // ✅ Get first record for reference fields like Admin_name, etc.
        $firstRecord = $deductions->first();

        // ✅ Find user's wallet
        $wallet = UserWallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['status' => false, 'message' => 'Wallet not found.'], 404);
        }

        // ✅ If user approves the deduction
        if ($request->action === 'yes') {
            // Deduct total points from wallet
            $wallet->total_points -= $totalDeductedPoints;
            $wallet->save();

			$lastHistory = PointDeductionHistory::where('user_id', $userId)->latest()->first();
       		$grossTotalPoints = $lastHistory ? $lastHistory->gross_total_points : $wallet->total_points;
			

			$wallet = UserWallet::where('user_id', $userId)->first();
    		$walletPoints = $wallet ? $wallet->total_points : 0;
            // ✅ Store record in main history
            PointDeductionHistory::create([
                'user_id' => $userId,
                'Admin_name' => $firstRecord->Admin_name,
                'Admin_type' => $firstRecord->Admin_type,
				'gross_total_points' => $grossTotalPoints,
				'remaining_points' => $walletPoints,
                'deducted_points' => $totalDeductedPoints,
                'status' => 'allowed',
                'date_time' => now(),
            ]);

            // ✅ Delete all temp records after approval
            TempPointDeductionHistory::where('user_id', $userId)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Points deducted successfully.',
                'remaining_points' => $wallet->total_points,
            ]);
        }

        // ✅ If user denies deduction
        if ($request->action === 'no') {
            TempPointDeductionHistory::where('user_id', $userId)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Points deduction request denied successfully.',
            ]);
        }

        // ⚠️ Invalid action
        return response()->json([
            'status' => false,
            'message' => 'Invalid action provided.',
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while processing your request.',
            'error' => $e->getMessage(),
        ], 500);
    }
}




}
