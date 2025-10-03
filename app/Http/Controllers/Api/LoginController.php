<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //

    // LoginController.php
    // public function login(Request $request)
    // {

    //     try {
    //         // Validate request

    //         $loginInput = $request->input('email') ?: $request->input('phone');

    //         // Trim and clean the input
    //         $loginInput = trim($loginInput);

    //         // Determine if login is email or phone
    //         $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

    //         // For phone numbers, remove any non-digit characters

    //         $user = User::where($fieldType, $loginInput)->first();

    //         if (! $user) {
    //             return response()->json(['message' => 'User not found'], 404);
    //         }

    //         if (! Hash::check($request->password, $user->password)) {
    //             return response()->json(['message' => 'Invalid password'], 401);
    //         }

    //         // Update FCM token
    //         $user->fcm = $request->fcm;
    //         $user->save();

    //         // Create Sanctum token
    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'message' => 'Logged in successfully',
    //             'token' => $token,
    //             'user' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name ?? null,
    //                 'email' => $user->email ?? null,
    //                 'phone' => $user->phone ?? null,
    //                 'image' => $user->image ?? null,
    //                 'country' => $user->country ?? null,
    //                 'fcm' => $user->fcm ?? null,
    //             ],
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Login failed: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }


	public function login(Request $request)
{
    try {
        // Validate request
        $loginInput = $request->input('email') ?: $request->input('phone');

        // Trim and clean the input
        $loginInput = trim($loginInput);

        // Determine if login is email or phone
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Find user
        $user = User::where($fieldType, $loginInput)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        // ✅ Toggle check
        // if ($user->toggle == 0) {
        //     return response()->json([
        //         'message' => 'Your account has been deactivated. Please check your email for details or contact the administrator.'
        //     ], 403);
        // }

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
            ],
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Login failed: '.$e->getMessage(),
        ], 500);
    }
}

    // Logout function
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
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
                'message' => 'Logged out failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
