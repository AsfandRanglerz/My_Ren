<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Farmer;
use App\Models\SubAdmin;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\AuthorizedDealer;
use App\Models\UserRolePermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function index() 
    {
        $notifications = Notification::all();
        $users = User::all();
        $subadmin = SubAdmin::all();

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


        return view('admin.notification.index', compact('notifications', 'sideMenuPermissions' , 'users' , 'subadmin'));
    }
    
    public function store(Request $request) 
{
    $validated = $request->validate([
        'user_type' => 'required|in:all,users',
        'message' => 'required|string|max:1000',
    ]
    // , 
    // // [
    //     'user_type.required' => 'User Type is required',
    // ]
);

    // $status = '0';

    // $userNames = is_array($request->user) ? $request->user : [$request->user];
    // $user_type = $validated['user_type'];
    // $message = $validated['message'];

    // // foreach ($usersNames as $userName) {
    //     $notification = Notification::create([
    //         'user_type' => $user_type,
    //         'message' => $message,
    //     ]);

        // $customer = User::find($userName);
        // if ($users && $user->fcm_token) {
        //     $data = [
        //         'id' => $notification->id,
        //         'title' => $request->title,
        //         'body' => $request->description,
        //     ];
        //     dispatch(new NotificationJob($user->fcm_token, $request->title, $request->description, $data));
        // }

        // If `$driver` variable exists, you need to define it OR use a loop for multiple drivers like you did for customers
        // Example: 
        //Note: in my case, I have no user = driver u can edit with respect to your logic
        // $drivers = is_array($request->drivers) ? $request->drivers : [$request->drivers];
    //     foreach ($drivers as $driver) {
    //         $driverUser = Driver::find($driver);
    //         if ($driverUser && $driverUser->fcm_token) {
    //             $data = [
    //                 'id' => $notification->id,
    //                 'title' => $request->title,
    //                 'body' => $request->description,
    //             ];
    //             dispatch(new NotificationJob($driverUser->fcm_token, $request->title, $request->description, $data));
    //         }
    //     }
    // }

    if (Auth::guard('subadmin')->check()) {
        SubAdminLog::create([
            'subadmin_id' => Auth::guard('subadmin')->id(),
            'section' => 'Notifications',
            'action' => 'Add',
            'message' => 'Added Notification',
        ]);
    }

    return redirect()->route('notification.index')->with(['message' => 'Notification saved successfully! It will be sent later.']);
}

 public function destroy(Request $request, $id)
    {
         $notification = Notification::find($id);
        // $notificationName = $notification->name;
        if (Auth::guard('subadmin')->check()) {
            $subadmin = Auth::guard('subadmin')->user();
            $subadminName = $subadmin->name;
            SubAdminLog::create([
                'subadmin_id' => Auth::guard('subadmin')->id(),
                'section' => 'Notifications',
                'action' => 'Delete',
                'message' => "SubAdmin: {$subadminName} Deleted Notification",
            ]);
        }
        $notification->delete();
        return redirect()->route('notification.index')->with(['message' => 'Notification Deleted Successfully']);
 
    }
 
    public function deleteAll()
{
    Notification::truncate();  // or Notification::query()->delete(); if you want model events to trigger
    return redirect()->route('notification.index')->with('message', 'All notifications have been deleted.');
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
