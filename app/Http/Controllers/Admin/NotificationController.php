<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotificationJob;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\SubAdmin;
use App\Models\User;
use App\Models\UserRolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {

        $notifications = AdminNotification::latest()->get();

        $users = User::all();

        $subadmin = SubAdmin::all();

        $sideMenuPermissions = collect();

        // ✅ Check if user is not admin (normal subadmin)

        if (! Auth::guard('admin')->check()) {

            $user = Auth::guard('subadmin')->user()->load('roles');

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

        return view('admin.notification.index', compact('notifications', 'sideMenuPermissions', 'users', 'subadmin'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'user_type' => 'required',

        ],
            [
                'user_type.required' => 'User Type is required',
            ]);

        AdminNotification::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Iterate through the arrays and create notifications
        foreach ($request->users as $userId) {
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $request->title,
                'description' => $request->description,
                'created_at' => now(),
            ]);

            //        $customer = User::find($userId);
            // if ($customer && $customer->fcm_token) {
            //     $data = [
            //         'id' => $notification->id,
            //         'title' => $request->title,
            //         'body' => $request->description,

            //     ];
            //     dispatch(new NotificationJob($customer->fcm_token, $request->title, $request->description, $data));
        }

        return redirect()->route('notification.index')->with(['success' => 'Notification Sent Successfully']);
    }

    public function destroy(Request $request, $id)
    {

        $notification = AdminNotification::find($id);
        $notification->delete();

        return redirect()->route('notification.index')->with(['success' => 'Notification Deleted Successfully']);
    }

    public function deleteAll()
    {

        AdminNotification::truncate();  // or Notification::query()->delete(); if you want model events to trigger

        return redirect()->route('notification.index')->with(['success' => 'All notifications have been deleted']);

    }

    public function getUsersByType(Request $request)
    {

        $type = $request->type;

        $users = [];

        switch ($type) {

            case 'subadmin':

                $users = SubAdmin::select('id', 'name', 'email')->get();

                break;

            case 'web':

                $users = User::select('id', 'name', 'email')->get();

                break;

        }

        return response()->json($users);

    }
}
