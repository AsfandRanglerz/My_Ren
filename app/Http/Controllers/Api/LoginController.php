<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Models\LoginTracking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    //



// LoginController.php
public function login(Request $request)
{


    try {
        // Validate request
        

        $loginInput = $request->input('email') ?: $request->input('phone');
       
        
        // Trim and clean the input
        $loginInput = trim($loginInput);
        
        // Determine if login is email or phone
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        

        // For phone numbers, remove any non-digit characters
        if ($fieldType === 'phone') {
            $loginInput = preg_replace('/[^0-9]/', '', $loginInput);
        }

        $user = User::where($fieldType, $loginInput)->first();
        

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        // Update FCM token
        $user->fcm = $request->fcm;
        $user->save();
       

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? null,
                'email' => $user->email ?? null,
                'phone' => $user->phone ?? null,
                'image' => $user->image ?? null,
                'country' => $user->country ?? null,
                'fcm' => $user->fcm ?? null,
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Login failed: ' . $e->getMessage()
        ], 500);
    }
}

    // Logout function
public function logout(Request $request)
{
    try {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
                
            ], 401);
        }

        
        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Logged out failed: ' . $e->getMessage(),
        ], 500);
    }
}


 

}