<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserWallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletUserPointController extends Controller
{
    public function getPoint()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

           
            $totalPoints = UserWallet::where('user_id', $user->id)
                ->sum('total_points');

           
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $monthlyPoints = UserWallet::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_points');

           
            $userRanks = UserWallet::select('user_id', DB::raw('SUM(total_points) as total'))
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->get();

         
            $rank = $userRanks->search(function ($wallet) use ($user) {
                return $wallet->user_id == $user->id;
            });

            $rank = $rank !== false ? $rank + 1 : null;

            return response()->json([
                'message' => 'Wallet Stats Fetched Successfully',
                'user_id' => $user->id,
                'total_points' => $totalPoints,
                'monthly_points' => $monthlyPoints,
                'rank' => $rank,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}