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

    public function ClaimVoucher()
    {
        $data = ClaimVoucher::with('user')
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
