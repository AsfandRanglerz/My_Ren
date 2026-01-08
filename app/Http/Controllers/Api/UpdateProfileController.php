<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UpdateProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            $type = $request->query('type', 'email'); // default to email

            if ($type === 'phone') {
                $data = $user->only(['user_name', 'phone']);
            } else {
                $data = $user->only(['user_name', 'email']);
            }

            return response()->json([
                'message' => 'Profile fetched successfully',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
public function sendOtp(Request $request)
{
    $user = Auth::user();
    $type = $request->input('type'); // 'email' or 'phone'

    if (!in_array($type, ['email', 'phone'])) {
        return response()->json(['message' => 'Invalid type'], 422);
    }

    $otp = rand(1000, 9999);
    $otpToken = Str::uuid();

    // Prepare the OTP record based on type
    $otpData = [
        'otp' => $otp,
        'otp_token' => $otpToken,
    ];

    if ($type === 'email') {
        $otpData['email'] = $user->email;
    } else {
        $otpData['phone'] = $user->phone;
    }

    EmailOtp::create($otpData);

    // You can now send the OTP via email or SMS here
    // For now, we just return it (remove this in production)
    return response()->json([
        'message' => "OTP sent to your $type",
        'otp_token' => $otpToken,
        'otp' => $otp // Remove this in production
    ]);
}
public function verifyOtp(Request $request)
{
    $user = Auth::user();
    $type = $request->input('type'); // email or phone
    $providedOtp = $request->input('otp');

    $hashedOtp =  $providedOtp;

    $token = PersonalAccessToken::where('tokenable_type', get_class($user))
        ->where('tokenable_id', $user->id)
        ->where('name', 'otp_' . $type)
        ->where('token', $hashedOtp)
        ->latest()
        ->first();

    if ($token) {
        // Optional: delete used OTP
        $token->delete();

        return response()->json(['message' => 'OTP verified successfully']);
    }

    return response()->json(['message' => 'Invalid OTP'], 422);
}

}