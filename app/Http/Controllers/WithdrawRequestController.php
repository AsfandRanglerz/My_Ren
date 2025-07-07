<?php

namespace App\Http\Controllers;


use Log;
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

    return response()->json(['success' => true]);


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
