<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivePoint;
use App\Models\UserActivePointsHistory;
use App\Models\UserWallet;
use App\Models\LoginRewardRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class UserActivePointsController extends Controller
{
    //
    public function handleUserActiveReward($userId)
    {
        DB::transaction(function () use ($userId) {
            $today = now()->startOfDay();

            $activePoints = UserActivePoint::firstOrNew(['user_id' => $userId]);

            $lastActiveDate = $activePoints->last_active_date
                ? Carbon::parse($activePoints->last_active_date)->startOfDay()
                : null;

            // ✅ Decide day counter
            if (!$lastActiveDate) {
                // Pehli dafa active
                $activePoints->day_counter = 1;
                $activePoints->first_active_date = $today;
            } else {
                $daysDiff = $today->diffInDays($lastActiveDate);

                if ($daysDiff === 0) {
                    // Same day → kuch nahi
                } elseif ($daysDiff === 1) {
                    $activePoints->day_counter += 1;
                } else {
                    // Break → reset
                    $activePoints->day_counter = 1;
                    $activePoints->first_active_date = $today;
                    $activePoints->current_points = 0;
                }
            }

            $activePoints->last_active_date = $today;

            // ✅ Reward rule
            $rule = LoginRewardRule::where('day', $activePoints->day_counter)->first();
            $rulePoints = $rule ? $rule->points : 0;

            // ✅ Withdraw logic
            $latestWithdraw = $this->getLatestWithdrawPoints($userId);

            $newPoints = max($rulePoints - ($latestWithdraw > 0 ? $latestWithdraw : $activePoints->current_points), 0);

            $activePoints->current_points += $newPoints;
            $activePoints->save();

            // ✅ History
            UserActivePointsHistory::create([
                'user_id' => $userId,
                'points_awarded' => $newPoints,
                'source' => 'active_reward',
                'day_counter' => $activePoints->day_counter,
                'remarks' => 'Day ' . $activePoints->day_counter . ' Reward',
            ]);

            // ✅ Wallet
            $wallet = UserWallet::firstOrCreate(['user_id' => $userId]);
            $wallet->total_points += $newPoints;
            $wallet->save();
        });

        return response()->json(['status' => 'ok']);
    }

    /**
     * ✅ Latest withdraw ya last earned points check
     */
    private function getLatestWithdrawPoints($userId)
    {
        return UserActivePointsHistory::where('user_id', $userId)
            ->where('source', 'withdraw')
            ->latest('created_at')
            ->value('points_awarded') ?? 0;
    }
}
