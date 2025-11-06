<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Mail\UserDeactivation;
use Illuminate\Validation\Rule;
use App\Models\UserRolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\PointDeductionHistory;
use App\Models\TempPointDeductionHistory;

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
    // Step 1: User ko fetch karo saare relations ke sath
    $data = User::with(['sales', 'pointDeductionHistories'])->find($id);

    if (! $data) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Step 2: UserWallet se total points nikalo
    $wallet = UserWallet::where('user_id', $id)->first();
    $walletPoints = $wallet ? $wallet->total_points : 0;

    // Step 3: PointDeductionHistory se last record nikalo
    $deduction = PointDeductionHistory::where('user_id', $id)->latest()->first();

    if (! $deduction) {
        // Record exist nahi karta to naya create karo
        $deduction = PointDeductionHistory::create([
            'user_id' => $id,
            'gross_total_points' => $walletPoints,
            'gross_remaining_points' => $walletPoints,
            'deducted_points' => 0,
        ]);

        $grossTotalPoints = $walletPoints;
        $remainingPoints = $walletPoints;
        $deductedPoints = 0;

    } else {
        // Record exist karta hai to current values lo
        $grossTotalPoints = $deduction->gross_total_points;
        $remainingPoints = $walletPoints;
        // $deductedPoints = $deduction->deducted_points;

        // ✅ Wallet points change handle karo (increase or decrease dono)
        if ($walletPoints != $deduction->gross_remaining_points) {

            // Agar points badhe to gross_total_points bhi badhao
            if ($walletPoints > $deduction->remaining_points) {
                $earnedDifference = $walletPoints - $deduction->remaining_points;
                $deduction->gross_total_points += $earnedDifference;
            }

            // Wallet ke current points ko always sync rakho
            $deduction->remaining_points = $walletPoints;
            $deduction->save();

            // Updated values set karo
            $grossTotalPoints = $deduction->gross_total_points;
            $remainingPoints = $walletPoints;
            // $deductedPoints = $deduction->deducted_points;
        }
    }

	 // ✅ Step 4: Requested Amount (from TempPointDeductionHistory)
    $requestedAmount = TempPointDeductionHistory::where('user_id', $id)->sum('deducted_points');

    // Step 4: View return karo
    return view('admin.sales.index', compact(
        'data',
        'grossTotalPoints',
        'remainingPoints',
		'requestedAmount'
    ));
}


public function deductPoints(Request $request)
{
	
    // ✅ Determine which guard is logged in (admin or subadmin)
    if (auth()->guard('admin')->check()) {
        $type = 'admin';
        $adminName = auth()->guard('admin')->user()->name;
    } elseif (auth()->guard('subadmin')->check()) {
        $type = 'subadmin';
        $adminName = auth()->guard('subadmin')->user()->name;
    } else {
        return response()->json(['message' => 'Unauthorized access'], 403);
    }

    // ✅ Fetch user and wallet
    $user = User::find($request->user_id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $wallet = UserWallet::where('user_id', $user->id)->first();

    if (!$wallet) {
        return response()->json(['message' => 'User wallet not found'], 404);
    }

    // ✅ Check if user has enough points
    if ($wallet->total_points < $request->deduct_points) {
        return response()->json(['message' => 'Insufficient points'], 400);
    }

	// ✅ Calculate total deducted points (sum from existing records)
  $totalDeducted = TempPointDeductionHistory::where('user_id', $user->id)->sum('deducted_points');

// ab total requested + pehle se deducted points ka sum
$totalAfterRequest = $totalDeducted + $request->deduct_points;

// agar yeh wallet ke total se zyada hai to insufficient
if ($totalAfterRequest > $wallet->total_points) {
    return response()->json([
        'message' => "You have already requested a total of {$totalDeducted} points. Adding this new request ({$request->deduct_points} points) would exceed your available balance of {$wallet->total_points} points. Insufficient points for this request."
    ], 400);
}



    // ✅ Save record in TempPointDeductionHistory
    TempPointDeductionHistory::create([
        'user_id' => $user->id,
        'Admin_name' => $adminName,
        'Admin_type' => $type,
        'deducted_points' => $request->deduct_points,
        'date_time' => now(),
    ]);

    // ✅ Redirect with success message
    return response()->json([
		'status' => true,
		'message' => 'Points deduction request sent successfully',
	], 200);
}




}
