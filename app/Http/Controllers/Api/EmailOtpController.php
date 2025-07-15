<?php



namespace App\Http\Controllers\Api;



use Log;

use App\Models\User;

use App\Models\EmailOtp;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;




class EmailOtpController extends Controller
{
      public function sendOtp(Request $request)
{
    try {
        // Custom manual validation logic
        $data = $request->only('email', 'phone', 'country');

        // At least one (email or phone) is required
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json(['error' => 'Email or phone is required'], 422);
        }

        // Validation Rules
        $rules = [];
        $messages = [];

        if (!empty($data['email'])) {
            $rules['email'] = [
                'email',
                'unique:users,email',
            ];
            $messages['email.unique'] = 'This email is already used';
        }

        if (!empty($data['phone'])) {
            $rules['phone'] = [
                'unique:users,phone',
            ];
            $messages['phone.unique'] = 'This phone number is already used';
        }

        // $rules['country'] = ['required', 'string', 'max:100'];

        // Run validation
        $request->validate($rules, $messages);

        // ✅ Generate 4-digit OTP & UUID token
        $otp = rand(1000, 9999);
        $otpToken = Str::uuid();

        // Insert into DB
        EmailOtp::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'otp' => $otp,
            'otp_token' => $otpToken,
            'expires_at' => null,
        ]);

        // Optional: Send mail
        // Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'OTP sent successfully',
        ], 200);

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

                    $data = $request->all();

        if (!empty($data['email'])) {
                $rules['email'] = [
                    'email',
                    'unique:users,email'
                ];
                $messages['email.unique'] = 'This email is already used';
            }

            if (!empty($data['phone'])) {
                $rules['phone'] = [
                    'unique:users,phone',
                ];
                $messages['phone.unique'] = 'This phone number is already used';
            }

        try {
            $request->validate([
                'otp' => 'required|numeric',
            ]);

            // Get the verified OTP record
            $otpRecord = EmailOtp::where('otp', $request->otp)->first();

            // Register the user with the OTP record
         

            return response()->json([
                'message' => 'OTP verified successfully',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


 public function registerUser(Request $request)
{
    try {
        $data = $request->all();

        $rules = [];
        $messages = [];

        if (!empty($data['email'])) {
            $rules['email'] = [
                'email',
                'unique:users,email'
            ];
            $messages['email.unique'] = 'This email is already used';
        }

        if (!empty($data['phone'])) {
            $rules['phone'] = [
                'unique:users,phone'
            ];
            $messages['phone.unique'] = 'This phone number is already used';
        }

        // Password validation
        $rules['password'] = ['required'];
        $rules['confirm_password'] = ['same:password'];

        $messages['confirm_password.same'] = 'Confirm Password must match the Password';

        // ✅ Validate the request
        $request->validate($rules, $messages);

        // ✅ Email ke zariye OTP record dhoondhna
        $otpRecord = EmailOtp::where('email', $request->email)->first();

        if (!$otpRecord) {
            return response()->json([
                'error' => 'OTP record not found for the given email'
            ], 404);
        }

        // ✅ User create karna
        $user = User::create([
            'email' => $otpRecord->email,
            'phone' => $otpRecord->phone,
            'country' => $otpRecord->country,
            'password' => Hash::make($request->password),
            'status' => is_null($otpRecord->phone) ? 1 : (is_null($otpRecord->email) ? 2 : null),
        ]);

        // ✅ OTP record delete karna
        $otpRecord->delete();

        return response()->json([
            'message' => 'Registered successfully',

        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Validation error',
            'messages' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}



}


