<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EmailOtp;
use Exception;

class EmailOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        try {
            $data = $request->only('email', 'phone', 'country');

            if (empty($data['email']) && empty($data['phone'])) {
                return response()->json(['error' => 'Email Or Phone Is Required'], 422);
            }

            $rules = [];
            $messages = [];

            if (!empty($data['email'])) {
                $rules['email'] = [
                    'required',
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    'unique:email_otps,email'
                ];
                $messages['email.email'] = 'Invalid Email Format';
                $messages['email.regex'] = 'Email Format Is Not Correct';
                $messages['email.unique'] = 'This Email Is Already Used';
            }

            if (!empty($data['phone'])) {
                $rules['phone'] = [
                    'required',
                    'regex:/^[0-9]{8,15}$/',
                    'unique:email_otps,phone'
                ];
                $messages['phone.regex'] = 'Phone Number Must Be 8 To 15 Digits';
                $messages['phone.unique'] = 'This Phone Number Is Already Used';
            }

            $rules['country'] = ['required', 'string', 'max:100'];
            $messages['country.required'] = 'Country Is Required';

            $request->validate($rules, $messages);

            $otp = rand(1000, 9999);
            $otpToken = Str::uuid();

            EmailOtp::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'otp' => $otp,
                'otp_token' => $otpToken,
                'expires_at' => null,
            ]);

            return response()->json([
                'message' => 'Otp Sent successfully',
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
        $rules = [];
        $messages = [];

        if (!empty($data['email'])) {
            $rules['email'] = [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:email_otps,email'
            ];
            $messages['email.email'] = 'Invalid Email Format';
            $messages['email.regex'] = 'Email Format Is Not Correct';
            $messages['email.unique'] = 'This Email Is Already Used';
        }

        if (!empty($data['phone'])) {
            $rules['phone'] = [
                'required',
                'regex:/^[0-9]{8,15}$/',
                'unique:email_otps,phone'
            ];
            $messages['phone.regex'] = 'Phone Number Must Be 8 To 15 Digits';
            $messages['phone.unique'] = 'This Phone Number Is Already Used';
        }

        try {
            $request->validate([
                'otp' => 'required|numeric',
            ]);

            $otpRecord = EmailOtp::where('otp', $request->otp)->first();

            return response()->json([
                'message' => 'OTP Verified Successfully',
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
                $messages['email.unique'] = 'This Email Is Already Used';
            }

            if (!empty($data['phone'])) {
                $rules['phone'] = [
                    'unique:users,phone'
                ];
                $messages['phone.unique'] = 'This Phone Number Is Already Used';
            }

            $rules['password'] = ['required'];
            $rules['confirm_password'] = ['same:password'];
            $messages['confirm_password.same'] = 'Confirm Password Must Match The Password';

            $request->validate($rules, $messages);

            $otpRecord = EmailOtp::where('email', $request->email)->first();

            if (!$otpRecord) {
                return response()->json([
                    'error' => 'OTP Record Not Found For The Given Email'
                ], 404);
            }

            $user = User::create([
                'email' => $otpRecord->email,
                'phone' => $otpRecord->phone,
                'country' => $otpRecord->country,
                'password' => Hash::make($request->password),
                'status' => is_null($otpRecord->phone) ? 1 : (is_null($otpRecord->email) ? 2 : null),
            ]);

            $otpRecord->delete();

            return response()->json([
                'message' => 'Registered Successfully',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something Went Wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLoggedInUserInfo()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json([
                'user_name' => $user->user_name ?? null,
                'image' => $user->image ?? null,
                'email' => $user->email ?? null,
                'phone' => $user->phone ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function requestUpdateOtp(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->only('email', 'phone', 'user_name', 'image');
            $updatedFields = [];

            if (!empty($data['user_name'])) {
                $user->user_name = $data['user_name'];
                $updatedFields[] = 'user_name';
            }

            if (!empty($data['image'])) {
                $user->image = $data['image'];
                $updatedFields[] = 'image';
            }

            if (empty($data['email']) && empty($data['phone']) && empty($updatedFields)) {
                return response()->json(['error' => 'No Data Provided To Update'], 422);
            }

            $rules = [];

            if ($user->status == 1) {
                if (!empty($data['email'])) {
                    return response()->json(['error' => 'Cannot Update Email'], 422);
                }

                if (!empty($data['phone'])) {
                    $rules['phone'] = [
                        'required',
                        'regex:/^[0-9]{8,15}$/',
                        'unique:users,phone'
                    ];
                }
            } elseif ($user->status == 2) {
                if (!empty($data['phone'])) {
                    return response()->json(['error' => 'Cannot Update Phone'], 422);
                }

                if (!empty($data['email'])) {
                    $rules['email'] = [
                        'required',
                        'email',
                        'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        'unique:users,email'
                    ];
                }
            }

            $request->validate($rules);

            if (!empty($updatedFields)) {
                $user->save();
            }

            if (!empty($data['email']) || !empty($data['phone'])) {
                $otp = rand(100000, 999999);
                $otpToken = Str::uuid();

                EmailOtp::create([
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'country' => 'N/A',
                    'otp' => $otp,
                    'otp_token' => $otpToken,
                    'expires_at' => now()->addMinutes(10),
                ]);

                return response()->json([
                    'message' => 'Otp Sent To Your Provided Contact Info',
                    'otp_token' => $otpToken,
                ], 200);
            }

            return response()->json(['message' => 'Profile Updated Successfully'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation Failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something Went Wrong ' . $e->getMessage()], 500);
        }
    }

    public function verifyAndUpdateContact(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'otp' => 'required|numeric',
            ]);

            $otpRecord = EmailOtp::where('otp', $request->otp)->first();

            if (!$otpRecord) {
                return response()->json(['error' => 'Invalid Otp'], 400);
            }

            if ($otpRecord->expires_at && now()->gt($otpRecord->expires_at)) {
                return response()->json(['error' => 'OTP Has Expired'], 400);
            }

            $updated = false;

            if (!empty($otpRecord->email)) {
                $user->email = $otpRecord->email;
                $updated = true;
            }

            if (!empty($otpRecord->phone)) {
                $user->phone = $otpRecord->phone;
                $updated = true;
            }

            if (!$updated) {
                return response()->json(['error' => 'Nothing To Update'], 422);
            }

            $user->save();
            $otpRecord->delete();

            return response()->json(['message' => 'Contact Info Updated Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something Went Wrong ' . $e->getMessage()], 500);
        }
    }
}
