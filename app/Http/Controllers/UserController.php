<?php

namespace App\Http\Controllers;

use App\Mail\UserDeactivation;
use App\Models\User;
use App\Models\UserRolePermission;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function Index()
    {
        $users = User::orderby('id', 'desc')->get();
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

        return view('users.index', compact('users', 'sideMenuPermissions'));

    }

    public function toggleStatus(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->toggle = $request->status;
            $user->save();
            // If deactivating and reason provided
            if ($request->status == 0 && $request->reason) {
                // Send email notification using Mailable
                Mail::to($user->email)->send(new UserDeactivation($user, $request->reason));
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $user->toggle ? 'Activated' : 'Deactivated',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    public function createview()
    {

        return view('users.create');

    }

    public function create(Request $request)
    {

        $request->validate([

            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'email' => [
                'required',
                'email',
                'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/',
                'unique:users,email',
            ],
            'phone' => 'required|regex:/^[0-9]+$/|max:15',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'Name is required',
            'image.image' => 'Image must be a valid image file',
            'image.mimes' => 'Image must be a jpeg, png, jpg, or gif file',
            'image.max' => 'Image size must not exceed 2MB',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'This email is already registered',
            'email.regex' => 'Email format is invalid',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must contain only digits',
            'phone.max' => 'Phone number must not exceed 15 digits',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters long',
        ]);

        $imagePath = null;

        // ✅ Check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Generate unique file name

            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            // Save image to public/admin/assets/images/user
            $image->move(public_path('admin/assets/images/users'), $imageName);
            // Store path to save in database (if needed)
            $imagePath = 'admin/assets/images/users/'.$imageName;
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'image' => $imagePath, // Make sure your users table has 'image' column
        ]);

        return redirect()->route('user.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::find($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => [
                'required',
                'email',
                'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'regex:/^\+[1-9]\d{1,14}$/',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'password' => 'nullable|min:6',
        ], [

            'name.required' => 'Name is required',
            'image.image' => 'Image must be a valid image file',
            'image.mimes' => 'Image must be a jpeg, png, jpg, or gif file',
            'image.max' => 'Image size must not exceed 2MB',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.regex' => 'Email format is invalid',
            'email.unique' => 'Email is already exist',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be in E.164 format like +923001234567.',
            'password.min' => 'Password must be at least 6 characters long',
            'phone.unique' => 'Phone number already exist',
        ]);
        $imagePath = $user->image;
        // ✅ Image update (if provided)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('admin/assets/images/users'), $imageName);
            $imagePath = 'admin/assets/images/users/'.$imageName;
            // ✅ Optional: Delete old image file (if exists)
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }
        }
        // ✅ Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'image' => $imagePath,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('user.index')->with('success', 'User updated successfully');

    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            UserWallet::where('user_id', $user->id)->delete();
            $user->delete();

            return redirect('/admin/user')->with('success', 'User and related wallets deleted successfully');
        } else {
            return redirect('/user')->with('error', 'User not found');
        }
    }

    // public function sales($id)
    // {
    //     $data = User::with('sales')->where('id', $id)->first();
    //     if (! $data) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }
    //     $totalPoints = $data->sales->sum('points_earned');
    //     // ✅ UserWallet update or create
    //     $wallet = UserWallet::where('user_id', $id)->first();
    //     if ($wallet) {
    //         // Agar wallet already exist karta hai, to points add karo
    //         $wallet->total_points += $totalPoints;
    //         $wallet->save();
    //     } else {
    //         // Wallet exist nahi karta, to new create karo
    //         UserWallet::create([
    //             'user_id' => $id,
    //             'total_points' => $totalPoints,
    //         ]);
    //     }

    //     return view('admin.sales.index', compact('data', 'totalPoints'));
    // }

public function sales($id)
{
    $data = User::with(['sales', 'pointDeductionHistories'])->find($id);

    if (! $data) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Total points earned from sales (gross total)
    $grossTotalPoints = UserWallet::find($id);
	return $grossTotalPoints = $grossTotalPoints ? $grossTotalPoints->total_points : 0;
	

    // Current wallet points
   

    // Last deduction history
    $deduction = $data->pointDeductionHistories()->latest()->first();

    if ($deduction) {
        $deductedPoints = $deduction->deducted_points ?? 0;
        $remainingPoints = $deduction->remaining_points ?? $wallet->total_points;
    } else {
        $deductedPoints = 0;
        $remainingPoints = 0; // abhi deduct koi nahi hua
    }

    return view('admin.sales.index', compact(
        'data',
        'grossTotalPoints',
        'deductedPoints',
        'remainingPoints'
    ));
}


public function deductPoints(Request $request)
{
    $user = User::find($request->user_id);
    $wallet = UserWallet::where('user_id', $user->id)->first();

    if($wallet->total_points < $request->deduct_points){
        return response()->json(['message' => 'Insufficient points'], 400);
    }

    // Deduct points from wallet
    $wallet->total_points -= $request->deduct_points;
    $wallet->save();

    // Save to PointDeductionHistory
    PointDeductionHistory::create([
        'user_id' => $user->id,
        'Admin_name' => auth()->user()->name,
        'Admin_type' => auth()->guard('admin')->check() ? 'admin' : 'subadmin',
        'deducted_points' => $request->deduct_points,
        'remaining_points' => $wallet->total_points,
        'total_points' => $wallet->total_points + $request->deduct_points,
        'gross_remaining_points' => $wallet->total_points,
        'gross_total_points' => $wallet->total_points + $request->deduct_points,
        'date_time' => now(),
    ]);

    return response()->json([
        'message' => 'Points deducted successfully',
        'remainingPoints' => $wallet->total_points,
        'deductedPoints' => $request->deduct_points
    ]);
}



}
