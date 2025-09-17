<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserEmailOtp;
use App\Models\EmailOtp;
use App\Models\User;
use App\Models\UserWallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class EmailOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        try {
            $data = $request->only('email', 'phone', 'country');

            $rules = [];
            $messages = [];

            if (! empty($data['email'])) {
                $rules['email'] = [
                    'unique:users,email',
                ];
                $messages['email.unique'] = 'This email is already taken';
            }

            if (! empty($data['phone'])) {
                $rules['phone'] = [
                    'unique:users,phone',
                ];
                $messages['phone.unique'] = 'This phone number is already taken';
            }

            $request->validate($rules, $messages);

            $otp = rand(1000, 9999);
            $otpToken = Str::uuid();

            $condition = [];

            if (! empty($request->email)) {
                $condition['email'] = $request->email;
            } else {
                $condition['phone'] = $request->phone;
            }

            EmailOtp::updateOrCreate(
                $condition,
                [
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'country' => $request->country,
                    'otp' => $otp,
                    'otp_token' => $otpToken,
                ]
            );

            if (! empty($request->email)) {
                Mail::to($request->email)->send(new UserEmailOtp($otp));
            } else {
                // Here you can implement sending OTP via SMS if needed
                $phone = $request->phone;
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
                $twilio->messages->create($phone, [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => "Dear user, your One-Time Password (OTP) is $otp. Please do not share this code with anyone. - RenSolutions",
                ]);
            }

            return response()->json([
                'message' => ! empty($request->email)
                    ? 'A verification OTP has been sent to your email'
                    : 'A verification OTP has been sent to your phone',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'error' => $firstError,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->all();

        try {
            // OTP base query
            $query = EmailOtp::where('otp', $request->otp);

            if (! empty($data['email'])) {
                $query->where('email', $data['email']);
            }

            if (! empty($data['phone'])) {
                $query->where('phone', $data['phone']);
            }

            $otpRecord = $query->latest()->first();

            if (! $otpRecord) {
                return response()->json([
                    'message' => 'Invalid OTP',
                ], 400);
            }

            return response()->json([
                'message' => 'OTP verified successfully',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function registerUser(Request $request)
    {
        try {
            $request->validate(
                [
                    'email' => 'nullable|unique:users,email',
                    'phone' => 'nullable|unique:users,phone',
                ],
                [
                    'email.unique' => 'This email is already taken.',
                    'phone.unique' => 'This phone number is already taken.',
                ]
            );

            // OTP Record Check
            $otpRecord = EmailOtp::where('email', $request->email)->first();
            if (! $otpRecord) {
                return response()->json([
                    'error' => 'OTP record not found for the given email.',
                ], 404);
            }

            // ✅ User Create
            $user = User::create([
                'email' => $otpRecord->email,
                'phone' => $otpRecord->phone,
                'country' => $otpRecord->country,
                'password' => Hash::make($request->password),
                'status' => is_null($otpRecord->phone) ? 1 : (is_null($otpRecord->email) ? 2 : null),
            ]);

            // ✅ Signup reward points read karo
            $rewardPoints = \App\Models\SignupRewardSetting::first();
            $points = $rewardPoints ? $rewardPoints->points : 0;

            // ✅ User wallet me insert karo
            $testing = \App\Models\UserWallet::create([
                'user_id' => $user->id,
                'total_points' => $points,
            ]);

            // ✅ Delete OTP record
            $otpRecord->delete();

            return response()->json([
                'message' => 'Registered successfully.',
                'user_id' => $user->id,
                'assigned_points' => $points,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json($e->errors(), 422);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getLoggedInUserInfo()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $totalPoints = UserWallet::with('user')
                ->where('user_id', $user->id)
                ->first();

            return response()->json([
                'name' => $user->name ?? null,
                'image' => $user->image ? 'public/'.$user->image : 'https://ranglerzwp.xyz/myren/public/admin/assets/images/avator.png',
                'country' => $user->country ?? null,
                'email' => $user->email ?? null,
                'phone' => $user->phone ?? null,
                'points' => $totalPoints ?? 0,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function requestUpdateOtp(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->only('email', 'phone', 'name', 'image');
            $updatedFields = [];

            // ✅ Name update
            if (! empty($data['name'])) {
                $user->name = $data['name'];
                $updatedFields[] = 'name';
            }

            // ✅ Image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $imagePath = 'admin/assets/images/users/';
                $image->move(public_path($imagePath), $imageName);

                $user->image = $imagePath.$imageName;
                $data['image'] = $user->image;
                $updatedFields[] = 'image';
            }

            // ✅ Check email or phone existence (new unique)
            if (! empty($data['email']) && $data['email'] !== $user->email) {
                $emailExists = User::where('email', $data['email'])
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($emailExists) {
                    return response()->json(['error' => 'Email is already taken'], 422);
                }
            }

            if (! empty($data['phone']) && $data['phone'] !== $user->phone) {
                $phoneExists = User::where('phone', $data['phone'])
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($phoneExists) {
                    return response()->json(['error' => 'Phone number is already taken'], 422);
                }
            }

            // ✅ Save name/image updates first if any
            if (! empty($updatedFields) && empty($data['email']) && empty($data['phone'])) {
                $user->save();

                return response()->json([
                    'message' => 'Profile updated successfully.',
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'image' => $user->image,
                    'country' => $user->country,
                ], 200);
            }

            // ✅ If email or phone updated → Send OTP
            if (! empty($data['email']) || ! empty($data['phone'])) {
                $otp = rand(1000, 9999);
                $otpToken = Str::uuid();

                EmailOtp::create([
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'name' => $data['name'] ?? null,
                    'image' => $data['image'] ?? null,
                    'country' => 'N/A',
                    'otp' => $otp,
                    'otp_token' => $otpToken,
                ]);

                if (! empty($data['email'])) {
                    Mail::to($data['email'])->send(new UserEmailOtp($otp));
                }

                if (! empty($data['phone'])) {
                    // ✅ Ensure Twilio format
                    $phone = $data['phone'];
                    if (substr($phone, 0, 1) !== '+') {
                        $phone = '+'.$phone; // ensure + laga ho
                    }
                    $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
                    $twilio->messages->create($phone, [
                        'from' => env('TWILIO_PHONE_NUMBER'),
                        'body' => "Dear user, your One-Time Password (OTP) is $otp. Please do not share this code with anyone. - RenSolutions",
                    ]);
                }

                // ✅ Message decide karo type ke hisaab se
                $msg = ! empty($data['phone'])
                    ? 'A verification OTP has been sent to your phone'
                    : 'A verification OTP has been sent to your email';

                return response()->json([
                    'message' => $msg,
                    'otp_token' => $otpToken,
                ], 200);
            }

            return response()->json(['message' => 'Profile updated successfully'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyAndUpdateContact(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $otpRecord = EmailOtp::where('otp', $request->otp)->first();

            if (! $otpRecord) {
                return response()->json(['error' => 'Invalid Otp'], 400);
            }

            $updated = false;

            if (! empty($otpRecord->email)) {
                $user->email = $otpRecord->email;
                $updated = true;
            }

            if (! empty($otpRecord->phone)) {
                $user->phone = $otpRecord->phone;
                $updated = true;
            }

            if (! $updated) {
                return response()->json(['error' => 'Nothing to update'], 422);
            }

            $user->save();
            $otpRecord->delete();

            return response()->json(['message' => 'Profile updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'.$e->getMessage()], 500);
        }
    }
}
