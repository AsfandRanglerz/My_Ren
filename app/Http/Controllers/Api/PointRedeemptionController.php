<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
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
		->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendings->isEmpty()) {
            return response()->json([
                'status' => 404,
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
            'status' => 200,
            'pending_requests' => $requests,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while checking for pending deductions.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function approveDeduction(Request $request)
{
    $userId = auth()->id();
    try {
        // ✅ Validate request
        if (!$request->has(['request_id', 'action'])) {
            return response()->json([
                'status' => false,
                'message' => 'Missing required fields (request_id, action).'
            ], 400);
        }

        // ✅ Fetch specific temp request
        $deduction = TempPointDeductionHistory::where('id', $request->request_id)
            ->where('user_id', $userId)
            ->first();

        if (!$deduction) {
            return response()->json([
                'status' => false,
                'message' => 'No pending deduction request found.'
            ], 404);
        }

        // ✅ Fetch wallet
        $wallet = UserWallet::where('user_id', $userId)->first();
        if (!$wallet) {
            return response()->json(['status' => false, 'message' => 'Wallet not found.'], 404);
        }

        // ✅ Common values
        $pointsToDeduct =  $deduction->deducted_points;
	
        $lastHistory = PointDeductionHistory::where('user_id', $userId)->latest()->first();
        $grossTotalPoints = $lastHistory ? $lastHistory->gross_total_points : $wallet->total_points;
        // ✅ Action = YES → Deduct and mark allowed
        if ($request->action === 'yes') {

            if ($wallet->total_points < $pointsToDeduct) {
                return response()->json(['status' => false, 'message' => 'Insufficient wallet points. You have only ' . $wallet->total_points . ' points available in your wallet.'], 400);
            }

            // Deduct points
            $wallet->total_points -= $pointsToDeduct;
            $wallet->save();

            // Add to main history
            PointDeductionHistory::create([
                'user_id' => $userId,
                'Admin_name' => $deduction->Admin_name,
                'Admin_type' => $deduction->Admin_type,
                'gross_total_points' => $grossTotalPoints,
                'remaining_points' => $wallet->total_points,
                'deducted_points' => $pointsToDeduct,
                'status' => 'allowed',
                'date_time' => $request->date_time ?? now(),
            ]);

            // Delete temp record
            $deduction->delete();

            return response()->json([
                'status' => true,
                'message' => 'Points deducted successfully.',
                'remaining_points' => $wallet->total_points,
            ]);
        }

        // ✅ Action = LATER → No deduction, mark pending_later
        if ($request->action === 'later') {

			$deduction->status = 'pending_later';
			$deduction->save();

            PointDeductionHistory::create([
                'user_id' => $userId,
                'Admin_name' => $deduction->Admin_name,
                'Admin_type' => $deduction->Admin_type,
                'gross_total_points' => $grossTotalPoints,
                'remaining_points' => $wallet->total_points,
                'deducted_points' => $pointsToDeduct,
                'status' => 'pending_later',
                'date_time' => $request->date_time ?? now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Points deduction saved as pending later.',
            ]);
        }

        // ⚠️ Invalid Action
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


public function PointsDeductionData()
{
    try {
		$userId = auth()->id();
        // ✅ Validate user
        if (!User::find($userId)) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // ✅ Allowed Deductions
        $allowedData = PointDeductionHistory::with('users')
            ->where('user_id', $userId)
            ->where('status', 'allowed')
            ->orderBy('id', 'desc')
            ->get();

        // ✅ Pending Later Deductions
        $pendingLaterData = PointDeductionHistory::with('users')
            ->where('user_id', $userId)
            ->where('status', 'pending_later')
            ->orderBy('id', 'desc')
            ->get();

        // ✅ Temporary Pending Deductions (from Temp table)
        $tempPendingData = TempPointDeductionHistory::with('users')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        // ✅ Response
        return response()->json([
            'status' => true,
            'Approved' => $allowedData,
            'Later' => $pendingLaterData,
            'Pending' => $tempPendingData,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong while fetching points deduction data.',
            'error' => $e->getMessage(),
        ], 500);
    }	
}



}
