<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;


class TwillioController extends Controller
{
     public function sendSms(Request $request)
    {
        $request->validate([
            'phone' => 'required',
        ]);

        try {
            $otp = rand(1000, 9999);
            $phone = $request->phone;
    
            // Send SMS
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $twilio->messages->create($phone, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Dear user, your One-Time Password (OTP) is $otp. Please do not share this code with anyone. - RenSolutions"
            ]);

            return response()->json(['status' => 'success', 'message' => 'SMS sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
