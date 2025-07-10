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
    try {
        // Custom manual validation logic
        $data = $request->only('email', 'phone');

        // ❗ Check: At least one (email or phone) is required
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json(['error' => 'Email or phone is required.'], 422);
        }

        // Validate email if provided
        if (!empty($data['email'])) {
            $request->validate([
                'email' => [
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    'unique:users,email'
                ],
            ]);
        }

        // Validate phone if provided
        if (!empty($data['phone'])) {
            $request->validate([
                'phone' => [
                    'regex:/^[0-9]{8,15}$/', // numeric and 8-15 digits
                ]
            ]);
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'phone' => $request->phone,
                'otp' => $otp,
                'expires_at' => null,
            ]
        );

        // Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully'], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong.',
            'message' => $e->getMessage()
        ], 500);
    }
}




    public function verifyOtp(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpRecord = EmailOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // ✅ OTP mil gaya – ab delete kar do
        $otpRecord->delete();

        return response()->json(['message' => 'OTP verified successfully'], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Agar validation fail ho
        return response()->json(['errors' => $e->errors()], 422);

    } catch (\Exception $e) {
        // Kisi aur unknown error ko pakar lo
        return response()->json([
            'error' => 'Something went wrong.',
            'message' => $e->getMessage()
        ], 500);
    }
}



}

