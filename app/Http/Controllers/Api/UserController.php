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
        'email' => 'required|email',
        'password' => 'required|min:6|same:confirm_password',
        'confirm_password' => 'required|min:6',
    ]);

    $otpRecord = EmailOtp::where('email', $request->email)->first();

    if (!$otpRecord) {
        return response()->json(['message' => 'No OTP record found.'], 404);
    }

    User::create([
        'name' => $request->name ?? null,
        'email' => $otpRecord->email,
        'phone' => $otpRecord->phone,
        'password' => bcrypt($request->password),
    ]);

    // OTP record delete after successful registration
    $otpRecord->delete();

    return response()->json(['message' => 'User registered successfully']);
}


public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
    ]);

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

    return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
}






}
