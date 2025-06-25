<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\LoginRewardRule;
use App\Models\UserRolePermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginRewardRuleController extends Controller
{
    //

    public function index()
    {
        $data = LoginRewardRule::all();

        $sideMenuPermissions = collect();

    // ✅ Check if user is not admin (normal subadmin)
    if (!Auth::guard('admin')->check()) {
        $user =Auth::guard('subadmin')->user()->load('roles');


        // ✅ 1. Get role_id of subadmin
        $roleId = $user->role_id;

        // ✅ 2. Get all permissions assigned to this role
        $permissions = UserRolePermission::with(['permission', 'sideMenue'])
            ->where('role_id', $roleId)
            ->get();

        // ✅ 3. Group permissions by side menu
        $sideMenuPermissions = $permissions->groupBy('sideMenue.name')->map(function ($items) {
            return $items->pluck('permission.name'); // ['view', 'create']
        });
    }
        return view('admin.loginrewardrule.index' , compact('data' , 'sideMenuPermissions'));
    }

    public function create()
    {
        return view('admin.loginrewardrule.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'day' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
        ]);



        LoginRewardRule::create([
            'day' => $request->day,
            'points' => $request->points,
        ])->save();
        

        return redirect()->route('login-reward-rules.index')->with('success', 'Login reward rule created successfully.');
    }

    public function edit($id)
    {
        $data = LoginRewardRule::findOrFail($id);
        return view('admin.loginrewardrule.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'day' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
        ]);

        $data = LoginRewardRule::findOrFail($id);
        $data->day = $request->day;
        $data->points = $request->points;
        $data->save();

        return redirect()->route('login-reward-rules.index')->with('success', 'Reward settings updated successfully.');
    }


    public function destroy($id)
    {
        $data = LoginRewardRule::findOrFail($id);
        $data->delete();

        return redirect()->route('login-reward-rules.index')->with('success', 'Reward Setting deleted successfully.');
    }
}
