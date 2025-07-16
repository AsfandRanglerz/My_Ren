<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginRewardRule;
use Exception;

class LoginRewardRuleController extends Controller
{
    // Fetch all login reward rules
    public function index()
    {
        try {
            $rules = LoginRewardRule::all();

            if ($rules->isEmpty()) {
                return response()->json([
                    'message' => 'No Login Reward Rules Found',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'message' => 'Login Reward Rules Fetched Successfully',
                'data'    => $rules
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error'   => $e->getMessage()
            ], 500);
}
}
}