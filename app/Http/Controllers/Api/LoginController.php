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
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $loginInput = $request->input('login');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($fieldType, $loginInput)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // ✅ 24-Hour Login Tracking Logic
        $cutoff = now()->subHours(24);
        $alreadyTracked = UserActivity::where('user_id', $user->id)
            ->where('login_at', '>=', $cutoff)
            ->exists();

        if (!$alreadyTracked) {
            UserActivity::create([
                'user_id' => $user->id,
                'is_active' => $request->has('is_active') ? $request->input('is_active') : 1,
                'login_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Login failed: ' . $e->getMessage()
        ], 500);
    }
}


public function logout(Request $request)
{
    try {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
                'status' => false
            ], 401);
        }

        LoginTracking::create([
            'user_id' => $user->id,
            'logout_at' => now(),
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
            'status' => true
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Logout failed: ' . $e->getMessage(),
            'status' => false
        ], 500);
    }
}


 private function trackActivityOnceEvery24Hours($userId)
    {
        $now = Carbon::now();
        $cutoff = $now->copy()->subHours(24);

        $alreadyExists = UserActivity::where('user_id', $userId)
            ->where('created_at', '>=', $cutoff)
            ->exists();

        if (!$alreadyExists) {
            UserActivity::create([
                'user_id' => $userId,
                'is_active' => 1,
                'created_at' => $now,
            ]);
        }
    }

}
