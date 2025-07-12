<?php



namespace App\Http\Controllers\Api;



use Log;

use App\Models\User;

use App\Models\EmailOtp;

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
            $data = $request->only('email', 'phone');

            // At least one (email or phone) is required
            if (empty($data['email']) && empty($data['phone'])) {
                return response()->json(['error' => 'Email or phone is required.'], 422);
            }

            // Validation Rules
            $rules = [];
            $messages = [];

            if (!empty($data['email'])) {
                $rules['email'] = [
                    'required',
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    'unique:email_otps,email'
                ];
                $messages['email.email'] = 'Invalid email format.';
                $messages['email.regex'] = 'Email format is not correct.';
                $messages['email.unique'] = 'This email is already used.';
            }

            if (!empty($data['phone'])) {
                $rules['phone'] = [
                    'required',
                    'regex:/^[0-9]{8,15}$/',
                    'unique:email_otps,phone'
                ];
                $messages['phone.regex'] = 'Phone number must be 8 to 15 digits.';
                $messages['phone.unique'] = 'This phone number is already used.';
            }

            // Run validation
            $request->validate($rules, $messages);

            // Generate OTP
            $otp = rand(1000, 9999);

            // Insert into DB
            EmailOtp::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'otp' => $otp,
                'expires_at' => null,
            ]);

            // Optional: Send mail
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

                    $data = $request->all();

        if (!empty($data['email'])) {
                $rules['email'] = [
                    'required',
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    'unique:email_otps,email'
                ];
                $messages['email.email'] = 'Invalid email format.';
                $messages['email.regex'] = 'Email format is not correct.';
                $messages['email.unique'] = 'This email is already used.';
            }

            if (!empty($data['phone'])) {
                $rules['phone'] = [
                    'required',
                    'regex:/^[0-9]{8,15}$/',
                    'unique:email_otps,phone'
                ];
                $messages['phone.regex'] = 'Phone number must be 8 to 15 digits.';
                $messages['phone.unique'] = 'This phone number is already used.';
            }

        try {
            $request->validate([
                'otp' => 'required|numeric',
            ]);

            // Get the verified OTP record
            $otpRecord = EmailOtp::where('otp', $request->otp)->first();

            // Register the user with the OTP record
         

            return response()->json([
                'message' => 'OTP verified successfully.',
                'user' => $data, 
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
    $data = $request->all();

    $rules = [];
    $messages = [];

    if (!empty($data['email'])) {
        $rules['email'] = [
            'required',
            'email',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            // 'unique:email_otps,email' // remove this because we're now finding the record
        ];
        $messages['email.email'] = 'Invalid email format.';
        $messages['email.regex'] = 'Email format is not correct.';
    }

    if (!empty($data['phone'])) {
        $rules['phone'] = [
            'required',
            'regex:/^[0-9]{8,15}$/',
        ];
        $messages['phone.regex'] = 'Phone number must be 8 to 15 digits.';
    }

    // Password validation
    $rules['password'] = ['required', 'min:6'];
    $rules['confirm_password'] = ['required', 'same:password'];

    $messages['password.required'] = 'Password is required.';
    $messages['password.min'] = 'Password must be at least 6 characters.';
    $messages['confirm_password.required'] = 'Confirm Password is required.';
    $messages['confirm_password.same'] = 'Confirm Password must match the Password.';

    // Validate the request
    $request->validate($rules, $messages);

    // ✅ Email ke zariye OTP record dhoondhna
    $otpRecord = EmailOtp::where('email', $request->email)->first();

    if (!$otpRecord) {
        return response()->json([
            'error' => 'OTP record not found for the given email.'
        ], 404);
    }

    // ✅ User create karna
    $user = User::create([
        'email' => $otpRecord->email,
        'phone' => $otpRecord->phone,
        'password' => Hash::make($request->password),
    ]);

    // ✅ OTP record delete karna
    $otpRecord->delete();

    return $user;
}


}


