<?php



namespace App\Http\Controllers\Api;



use App\Models\User;

use App\Models\EmailOtp;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class UserController extends Controller

{

    //



public function completeRegistration(Request $request)

{

    $request->validate([

        'password' => 'required|min:6|same:confirm_password',

    ]);



    // $otpRecord = EmailOtp::where('email', $request->email)->first();



    // if (!$otpRecord) {

    //     return response()->json(['message' => 'No OTP record found'], 404);

    // }



    User::create([

        'name' => $request->name ?? null,

        'email' => $request->email,

        'phone' => $request->phone,

        'password' => bcrypt($request->password),

    ]);



    // OTP record delete after successful registration

    // $otpRecord->delete();



    return response()->json(['message' => 'Registered successfully'], 200);

}





public function updateProfile(Request $request)

{

    $user = auth()->user();



    // Handle image upload

    if ($request->hasFile('image')) {

        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('uploads/users'), $imageName);

        $user->image = 'uploads/users/' . $imageName;

    }



    // Update name

    if ($request->name) {

        $user->name = $request->name;

    }



    $user->save();



    return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);

}













}

