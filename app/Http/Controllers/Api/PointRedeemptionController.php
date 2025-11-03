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

public function getPendingDeduction(Request $request)
{
    try {
        $userId = auth()->id(); // ✅ Logged-in user ID

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // ✅ Get all pending deductions for this user
        $pendings = TempPointDeductionHistory::where('user_id', $userId)->get();

        if ($pendings->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No pending deduction.',
            ], 404);
        }

        // ✅ Calculate total points pending
        $totalPendingPoints = $pendings->sum('deducted_points');

        // Optional: create a combined message for all pending requests
        $adminNames = $pendings->pluck('Admin_name')->unique()->join(', ');
        $message = "There is a Shopkeeper {$adminNames} wants to redeem your points ({$totalPendingPoints}). Do you allow this?";

        return response()->json([
            'status' => true,
            'message' => $message,
            'total_pending_points' => $totalPendingPoints,
            'requests' => $pendings->map(function($item){
                return [
                    'request_id' => $item->id,
                    'points' => $item->deducted_points,
                    'admin_name' => $item->Admin_name,
                    'admin_type' => $item->Admin_type,
                ];
            }),
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
        $temp = TempPointDeductionHistory::where('user_id', $userId)->latest()->first();

        if (!$temp) {
            return response()->json(['message' => 'No pending deduction request found.'], 404);
        }

        $wallet = UserWallet::where('user_id', $temp->user_id)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }

        // ✅ If user approves the deduction
        if ($request->action === 'yes') {
            // Deduct points from wallet
            $wallet->total_points -= $temp->deducted_points;
            $wallet->save();

            // Move data to main PointDeductionHistory
            PointDeductionHistory::create([
                'user_id' => $temp->user_id,
                'Admin_name' => $temp->Admin_name,
                'Admin_type' => $temp->Admin_type,
                'deducted_points' => $temp->deducted_points,
                'status' => 'approved',
                'date_time' => now(),
            ]);

            // ✅ Delete temp record after approval
            $temp->delete();

            return response()->json([
                'status' => true,
                'message' => 'Points deducted successfully.',
                'remaining_points' => $wallet->total_points,
            ], 200);
        }

        //  If user denies deduction
        if ($request->action === 'no') {
            // Just delete the temp request
            $temp->delete();

            return response()->json([
                'status' => true,
                'message' => 'Points deduction request denied successfully.',
            ], 200);
        }

        // ⚠️ If invalid action is sent
        return response()->json([
            'status' => false,
            'message' => 'Invalid action provided.'
        ], 400);

    } catch (\Exception $e) {
        // ⚠️ Catch any unexpected errors
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while processing your request.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
