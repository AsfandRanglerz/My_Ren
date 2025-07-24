<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\UserEmailOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class EmailOtpController extends Controller
{
public function sendOtp(Request $request)
{
    try {
        // $data = $request->only('email', 'phone', 'country');

        // $rules = [];
        // $messages = [];

        // if (!empty($data['email'])) {
        //     $rules['email'] = ['unique:users,email'];
        //     $messages['email.unique'] = 'This email is already used';
        // }

        // if (!empty($data['phone'])) {
        //     $rules['phone'] = ['unique:users,phone'];
        //     $messages['phone.unique'] = 'This phone number is already used';
        // }

        // $request->validate($rules, $messages);

        // $otp = rand(1000, 9999);
        // $otpToken = \Str::uuid();

        // $condition = [];

        // if (!empty($request->email)) {
        //     $condition['email'] = $request->email;
        // } else {
        //     $condition['phone'] = $request->phone;
        // }

        // // ✅ Insert/Update with required fields
        // EmailOtp::updateOrCreate(
        //     $condition,
        //     [
        //         'phone'      => $request->phone,
        //         'email'      => $request->email,
        //         'country'    => $request->country, // Make sure this is in your request
        //         'otp'        => $otp,
        //         'otp_token'  => $otpToken,
        //         'expires_at' => now()->addSeconds(50),
        //     ]
        // );

        // if (!empty($request->email)) {
        //     Mail::to($request->email)->send(new UserEmailOtp($otp, $request->name));
        // }

          $otp = rand(1000, 9999);
        $otpToken = \Str::uuid();


        EmailOtp::updateOrCreate(    [
                'phone'      => $request->phone,
                'email'      => $request->email,
                'country'    => $request->country, // Make sure this is in your request
                'otp'        => $otp,
                'otp_token'  => $otpToken,
                'expires_at' => now()->addSeconds(50),
            ]
        );

        return response()->json([
            'message' => !empty($request->email) 
                ? 'A verification OTP has been sent to your email.' 
                : 'A verification OTP has been sent to your phone.',
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

    try {
        // OTP base query
        $query = EmailOtp::where('otp', $request->otp);

        if (!empty($data['email'])) {
            $query->where('email', $data['email']);
        }

        if (!empty($data['phone'])) {
            $query->where('phone', $data['phone']);
        }

        $otpRecord = $query->latest()->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP.',
            ], 400);
        }

        if (now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'message' => 'OTP expired, please request a new one.',
            ], 410); // 410 Gone
        }

        return response()->json([
            'message' => 'OTP verified successfully.',
             'email' => $data['email'],
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


public function resendOtp(Request $request)
{
    try {
        $type = $request->query('type'); // 'phone' or 'email'
        $value = $request->query('value'); // actual phone or email value
        

        if (!in_array($type, ['phone', 'email']) || !$value) {
            return response()->json(['error' => 'Invalid Request'], 400);
        }

        // Check if an OTP was already sent recently
        $lastOtp = EmailOtp::where($type, $value)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 50) {
            $remaining = 50 - $lastOtp->created_at->diffInSeconds(now());
            return response()->json([
                'error' => 'OTP already sent recently. Please wait ' . $remaining . ' seconds.'
            ], 429);
        }

        // Generate new OTP and save
        $otp = rand(1000, 9999);
        $otpToken = Str::uuid();

        EmailOtp::create([
            'email' => $type === 'email' ? $value : null,
            'phone' => $type === 'phone' ? $value : null,
            'country' => 'N/A',
            'otp' => $otp,
            'otp_token' => $otpToken,
            'expires_at' => now()->addSeconds(50),
        ]);

        
        // if ($type === 'email') {
        //     Mail::to($value)->send(new UserEmailOtp($otp));
        // }

        return response()->json([
            'message' => ucfirst($type) . ' OTP resent successfully.',
            'otp_token' => $otpToken,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
         'message' => $e->getMessage()
        ], 500);
    }
}


    public function registerUser(Request $request)
    {
        try {
            

            if (!empty($data['email'])) {
               
            }

            if (!empty($data['phone'])) {
               
            }

           
            $otpRecord = EmailOtp::where('email', $request->email)->first();

            if (!$otpRecord) {
                return response()->json([
                    'error' => 'OTP record not found for the given email'
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
            return $otpRecord;

            return response()->json([
                'message' => 'Registered successfully',
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
                        'unique:users,phone'
                    ];
                }
            } elseif ($user->status == 2) {
                if (!empty($data['phone'])) {
                    return response()->json(['error' => 'Cannot Update Phone'], 422);
                }

                if (!empty($data['email'])) {
                    $rules['email'] = [
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
