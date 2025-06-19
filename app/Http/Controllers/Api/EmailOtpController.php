<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailOtp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmailOtpController extends Controller
{
    //

     public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|regex:/^[0-9]+$/|max:15',
    ]);

    
    $otp = rand(1000, 9999);

    EmailOtp::updateOrCreate(
        ['email' => $request->email],
        [
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]
    );

    // Mail::to($request->email)->send(new SendOtpMail($otp));

    return response()->json(['message' => 'OTP sent successfully.']);
}


    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $otpRecord = EmailOtp::where('email', $request->email)
        ->where('otp', $request->otp)
        ->where('expires_at', '>=', now())
        ->first();

    if (!$otpRecord) {
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    return response()->json(['message' => 'OTP verified successfully']);
}

}
