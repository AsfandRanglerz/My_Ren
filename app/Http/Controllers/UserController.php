<?php



namespace App\Http\Controllers;



use App\Models\Sale;

use App\Models\User;

use App\Models\UserWallet;

use Illuminate\Http\Request;

use App\Models\UserRolePermission;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;





class UserController extends Controller

{

    //



    public function Index()

    {

        $users = User::orderby('id', 'desc')->get();

    



        

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



        return view('users.index', compact('users' , 'sideMenuPermissions'));

    }



public function toggleStatus(Request $request)

{

    $user = User::find($request->id);

    

    if ($user) {

        $user->toggle = $request->status;

        $user->save();

        

        // If deactivating and reason provided

        if ($request->status == 0 && $request->reason) {

            // Save the reason (you might want to create a separate table for this)

            // $user->deactivation_reason = $request->reason;

            // $user->save();

            

            // Send email notification

            $this->sendDeactivationEmail($user, $request->reason);

        }

        

        return response()->json([

            'success' => true,

            'message' => 'Status updated successfully',

            'new_status' => $user->toggle ? 'Activated' : 'Deactivated'

        ]);

    }

    

    return response()->json([

        'success' => false,

        'message' => 'User not found'

    ], 404);

}



protected function sendDeactivationEmail($user, $reason)

{

    $data = [

        'name' => $user->name,

        'email' => $user->email,

        'reason' => $reason

    ];

    

    try {

        Mail::send('emails.user_deactivated', $data, function($message) use ($user) {

            $message->to($user->email, $user->name)

                    ->subject('Account Deactivation Notification');

        });

    } catch (\Exception $e) {

        \Log::error("Failed to send deactivation email: " . $e->getMessage());

    }

}





    public function createview() {

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

        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();



        // Save image to public/admin/assets/images/user

        $image->move(public_path('admin/assets/images/users'), $imageName);



        // Store path to save in database (if needed)

        $imagePath = 'admin/assets/images/users/' . $imageName;

        



        

    }



     



    // ✅ Save user

    User::create([

        'name' => $request->name,

        'email' => $request->email,

        'phone' => $request->phone,

        'password' => bcrypt($request->password),

        'image' => $imagePath, // Make sure your users table has 'image' column

    ]);





    return redirect()->route('user.index')->with('success', 'User created successfully');

}







    public function edit($id) {

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

        

    ],

        'phone' => 'required|regex:/^[0-9]+$/|max:15',

        'password' => 'nullable|min:6',

    ], [

        'name.required' => 'Name is required',

        'image.image' => 'Image must be a valid image file',

        'image.mimes' => 'Image must be a jpeg, png, jpg, or gif file',

        'image.max' => 'Image size must not exceed 2MB',

        'email.required' => 'Email is required',

        'email.email' => 'Email must be a valid email address',

        'email.regex' => 'Email format is invalid',

        'phone.required' => 'Phone number is required',

        'phone.regex' => 'Phone number must contain only digits',

        'phone.max' => 'Phone number must not exceed 15 digits',

        'password.min' => 'Password must be at least 6 characters long',

    ]);



    $imagePath = $user->image;



    // ✅ Image update (if provided)

    if ($request->hasFile('image')) {

        $image = $request->file('image');

        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('admin/assets/images/users'), $imageName);

        $imagePath = 'admin/assets/images/users/' . $imageName;



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




    public function delete($id) {

        $user = User::find($id);

        if ($user) {

            $user->delete();

            return redirect('/admin/user')->with('success', 'User deleted successfully');

        } else {

            return redirect('/user')->with('error', 'User not found');

        }

    }









public function sales($id)
{
    $data = User::with('sales')->where('id', $id)->first();

    if (!$data) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $totalPoints = $data->sales->sum('points_earned');

    // ✅ UserWallet update or create
    $wallet = UserWallet::where('user_id', $id)->first();

    if ($wallet) {
        // Agar wallet already exist karta hai, to points add karo
        $wallet->total_points += $totalPoints;
        $wallet->save();
    } else {
        // Wallet exist nahi karta, to new create karo
        UserWallet::create([
            'user_id' => $id,
            'total_points' => $totalPoints,
        ]);
    }

    return view('admin.sales.index', compact('data', 'totalPoints'));
}




    

}

