<?php

namespace App\Http\Controllers;


use Log;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawRequests;


class WithdrawRequestController extends Controller
{

public function index()
{
        $withdrawRequests = WithdrawRequest::all();
        return view('admin.withdrawrequest.index', compact('withdrawRequests'));
}

public function update(WithdrawRequests $request, $id)
{
    try {
        $withdrawRequest = WithdrawRequest::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('admin/assets/images/users'), $imageName);
            $withdrawRequest->attachment = 'admin/assets/images/users/' . $imageName;
        }

        // Convert status
        $withdrawRequest->status = $request->status === 'approved' ? 0 : 1;

        $withdrawRequest->save();

        // ✅ Check if attachment is set AND status is approved (0)
        if (!empty($withdrawRequest->attachment) && $withdrawRequest->status == 0) {
            // Get user_id from the withdraw request
            $userId = $withdrawRequest->user_id;

            // Get voucher points to deduct
            $pointsToDeduct = $withdrawRequest->voucher_points;

            // Find user's wallet
            $userWallet = UserWallet::where('user_id', $userId)->first();

            if ($userWallet) {
                // ✅ Subtract points safely
                $userWallet->total_points = max(0, $userWallet->total_points - $pointsToDeduct);
                $userWallet->save();
            }
        }

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // Log the error if needed: Log::error($e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function delete($id)
{
        $withdrawRequest = WithdrawRequest::findOrFail($id);
        $withdrawRequest->delete();

        return redirect()->route('withdraw.requests')->with('success', 'Withdraw request deleted successfully');
}

public function withdrawalCounter(){
        $orderCount = WithdrawRequest::whereNull('status')
        ->count();
         return response()->json(['count' => $orderCount]);
}

}
