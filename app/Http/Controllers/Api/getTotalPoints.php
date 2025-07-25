<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class getTotalPoints extends Controller
{
    //

    public function getTotalPoints(Request $request)
{
    try {
        $userId = Auth::id(); // or auth()->id()

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user',
            ], 401);
        }

        $totalPoints = DB::table('user_wallets')
            ->where('user_id', $userId)
            ->value('total_points');

        return response()->json([
            'status' => true,
            'total_points' => $totalPoints ?? 0,
        ]);

    } catch (\Exception $e) {
return response()->json([
    'status' => false,
    'message' => 'Something went wrong',
    'error' => $e->getMessage(),
]);

}

}

}