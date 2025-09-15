<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\ForgotOTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
public function forgotPassword(Request $request)
{
     try {
        $type = $request->type; // 'email' or 'phone'
        $identifier = $request->identifier; // This will hold either email or phone

        // Validate type existence
        if (!in_array($type, ['email', 'phone'])) {
            return response()->json(['message' => 'Invalid type provided'], 400);
        }

        // Determine if input is an email or phone
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $isPhone = preg_match('/^[0-9]{7,15}$/', $identifier); // basic phone number pattern

        // Cross validation: If type = email but phone entered
        if ($type === 'email' && !$isEmail) {
            return response()->json(['message' => 'Email is required'], 400);
        }

        // If type = phone but email entered
        if ($type === 'phone' && !$isPhone) {
            return response()->json(['message' => 'Phone number is required'], 400);
        }

        // Find user by email or phone
        $user = User::where($type, $identifier)->first();

        $label = $type === 'phone' ? 'Phone number' : 'Email';

        if (!$user) {
            return response()->json([
                'message' => $label . ' does not exist'
            ], 404);
        }

        // Generate OTP and token
        $otp = rand(1000, 9999);
        $otpToken = Str::uuid();

        // Store in database
        EmailOtp::create([
            $type => $identifier,
            'otp' => $otp,
            'otp_token' => $otpToken,
        ]);

         if ($type === 'email') {
            Mail::to($identifier)->send(new ForgotOTPMail($otp));
        }
        if ($type === 'phone') {
            // Send SMS
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $twilio->messages->create($identifier, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Dear user, your One-Time Password (OTP) is $otp. Please do not share this code with anyone. - RenSolutions"
            ]);
        }
        return response()->json([
            'message' => 'OTP sent successfully to your email',
            'otp_token' => $otpToken
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }


}

public function forgotverifyOtp(Request $request)
{
   try {
        // Validate input
        $request->validate([
            'otp' => 'required|digits:4',
            // 'otp_token' => 'required'
        ]);

        // Find OTP record
        $otpRecord = EmailOtp::where('otp_token', $request->otp_token)->latest()->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP token'
            ], 400);
        }

        // Check OTP value
        if ($otpRecord->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 402);
        }

        // Match user by email or phone
        $user = null;

        if ($otpRecord->email) {
            $user = User::where('email', $otpRecord->email)->first();
        } elseif ($otpRecord->phone) {
            $user = User::where('phone', $otpRecord->phone)->first();
        }

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Mark OTP as verified
        $otpRecord->update(['verified' => true]);

        return response()->json([
            'message' => 'OTP verified successfully',
            'otp_token' => $otpRecord->otp_token,
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function resendOtp(Request $request)
{
    try {
        $type = $request->type;
        $identifier = $request->identifier;

        if (!in_array($type, ['email', 'phone'])) {
            return response()->json(['message' => 'Invalid type provided'], 400);
        }

        // Same 50-second check
        $recentOtp = EmailOtp::where($type, $identifier)
            ->latest()
            ->first();

        

        $otp = rand(1000, 9999);
        $otpToken = Str::uuid();

        $recentOtp->update([
                'otp' => $otp,
                'otp_token' => $otpToken,
            ]);

        if ($type === 'email') {
            Mail::to($identifier)->send(new ForgotOTPMail($otp));
        }
         if ($type === 'phone') {
            // Send SMS
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $twilio->messages->create($identifier, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Dear user, your One-Time Password (OTP) is $otp. Please do not share this code with anyone. - RenSolutions"
            ]);
        }

        return response()->json([
            'message' => 'OTP resent successfully',
            'otp_token' => $otpToken,
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function resetPassword(Request $request)
{
    
   try {
        // Validate input
        $request->validate([
            'otp_token' => 'required|uuid',
            'new_password' => 'required|min:8',
        ]);

        // Fetch OTP record using token
        $otpRecord = EmailOtp::where('otp_token', $request->otp_token)->first();

        if (!$otpRecord || !$otpRecord->verified) {
            return response()->json(['message' => 'Invalid or unverified OTP token'], 400);
        }

        // Find the user by email or phone
         if($otpRecord->email !== null) {
            $user = User::where('email', $otpRecord->email)->first();
        } elseif($otpRecord->phone !== null) {
            $user = User::where('phone', $otpRecord->phone)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Prevent reuse of old password
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'message' => 'This password is already in use. Please choose a different password',
            ], 422);
        }

        // Update password
         $user->password = Hash::make($request->new_password);
        $user->save();

        // Delete OTP record
        $otpRecord->delete();

        return response()->json([
            'message' => 'Password reset successfully',
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
