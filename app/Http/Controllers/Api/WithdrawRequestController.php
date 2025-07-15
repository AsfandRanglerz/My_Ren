<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Http\Controllers\Controller;

class WithdrawRequestController extends Controller
{
    //

   public function store(Request $request)
{
    try {
        
        $user_id = $request->user_id; 

        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $voucher_id = $request->voucher_id;
        $voucher = Voucher::find($voucher_id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $existingRequest = WithdrawRequest::where('user_id', $user_id)
            ->where('voucher_id', $voucher_id)
            ->whereNull('status')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'Your request is already sent to admin'
            ], 409); // 409 = Conflict
        }

        // ✅ Create a new withdrawal request
        $withdrawalRequest = new WithdrawRequest();
        $withdrawalRequest->user_id = $user_id;
        $withdrawalRequest->name = $user->name;
        $withdrawalRequest->voucher_id = $voucher_id;
        $withdrawalRequest->withdrawal_method = $request->withdrawal_method;
        $withdrawalRequest->withdrawal_amount = $voucher->amount;
        $withdrawalRequest->voucher_points = $voucher->required_points;
        $withdrawalRequest->withdrawal_details = $request->withdrawal_details;
        $withdrawalRequest->account_number = $request->account_number;
        $withdrawalRequest->status = null; // Pending status

        $withdrawalRequest->save();

        return response()->json(['message' => 'Withdrawal request submitted successfully'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}

}
