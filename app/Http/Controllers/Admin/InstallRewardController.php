<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallReward;
use App\Models\LoginRewardRule;
use App\Models\UserRolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class InstallRewardController extends Controller
{
    //

    public function index()
    {
        $data = InstallReward::all();
        
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
        'target_sales' => 'required|string|max:255|unique:install_rewards,target_sales',
        'points' => 'required|integer|min:0',
    ], [
        'target_sales.unique' => 'This target sales value already exists, please enter a different value.'
    ]);

    InstallReward::create([
        'target_sales' => $request->target_sales,
        'points' => $request->points,
    ]);

    return redirect()->route('intall-rewards.index')
                     ->with('success', 'Install reward created successfully');
}


    public function edit($id)
    {
        $data = InstallReward::findOrFail($id);
        return view('admin.loginrewardrule.edit', compact('data'));
    }

   public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'target_sales' => 'required|string|unique:install_rewards,target_sales,' . $id,
        'points' => 'required|numeric',
    ], [
        'target_sales.unique' => 'This target sales value already exists, please enter a different value.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $data = InstallReward::findOrFail($id);
    $data->target_sales = $request->target_sales;
    $data->points = $request->points;
    $data->save();

    return redirect()->route('intall-rewards.index')
                     ->with('success', 'Install reward updated successfully');
}


    public function destroy($id)
    {
        $data = InstallReward::findOrFail($id);
        $data->delete();

        return redirect()->route('intall-rewards.index')->with('success', 'Install reward deleted successfully');
    }
}
