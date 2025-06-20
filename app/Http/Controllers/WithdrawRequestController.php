<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Http\Controllers\Controller;

class WithdrawRequestController extends Controller
{
    public function withdrawRequests()
    {
        $withdrawRequests = WithdrawRequest::all();
        return view('admin.withdrawrequest.index', compact('withdrawRequests'));
    }

    public function withdrawRequestcreate(Request $request, $id)
{
    $request->validate([
        'attachment' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
    ]);

    $withdrawRequest = WithdrawRequest::findOrFail($id);

     if ($request->hasFile('attachment')) {
        $image = $request->file('attachment');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('admin/assets/images/users'), $imageName);
        $imagePath = 'admin/assets/images/users/' . $imageName;
    }

    WithdrawRequest::create([
        'image' => $imagePath ?? null,
    ]);
    

    return redirect()->back()->with('message', 'Withdraw request updated successfully');
}

}
