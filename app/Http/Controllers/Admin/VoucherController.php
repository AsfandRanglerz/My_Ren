<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimVoucher;
use App\Models\UserRolePermission;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    //

    public function index()
    {
        $vouchers = Voucher::orderBy('id', 'desc')->get();

        return view('admin.voucher.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.voucher.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|unique:vouchers,required_points',
            'rupees' => 'required|numeric',
        ], [
            'points.unique' => 'Against these number of points voucher has already been created.',
        ]);

        // 4 digit unique voucher code
        $voucherCode = '#'.str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        Voucher::create([
            'required_points' => $request->points,
            'rupees' => $request->rupees,
            'voucher_code' => $voucherCode,
        ]);

        return redirect()->route('voucher.index')->with('success', 'Voucher created successfully');
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);

        return view('admin.voucher.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'points' => 'required|integer|unique:vouchers,required_points,'.$id,
            'rupees' => 'required|numeric',
        ], [
            'points.unique' => 'Against these number of points voucher has been already created.',

        ]);

        $voucher = Voucher::findOrFail($id);
        $voucher->update([
            'required_points' => $request->points,
            'rupees' => $request->rupees,
        ]);

        return redirect()->route('voucher.index')->with('success', 'Voucher updated successfully');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('voucher.index')->with('success', 'Voucher deleted successfully');

    }

    public function ClaimVoucher()
    {
        $data = ClaimVoucher::with('user', 'voucher')
            ->orderBy('created_at', 'desc')
            ->get();

        ClaimVoucher::where('is_seen', 0)->update(['is_seen' => 1]);

        $sideMenuPermissions = collect();

        // ✅ Check if user is not admin (normal subadmin)
        if (! Auth::guard('admin')->check()) {
            $user = Auth::guard('subadmin')->user()->load('roles');

            // ✅ 1. Get role_id of subadmin
            $roleId = optional($user->roles->first())->id;

            // ✅ 2. Get all permissions assigned to this role
            $permissions = UserRolePermission::with(['permission', 'sideMenue'])
                ->where('role_id', $roleId)
                ->get();
            // ✅ 3. Group permissions by side menu
            $sideMenuPermissions = $permissions->groupBy('sideMenue.name')->map(function ($items) {
                return $items->pluck('permission.name'); // ['view', 'create']
            });
        }

        return view('admin.voucher.claimed', compact('data', 'sideMenuPermissions'));

    }
}
