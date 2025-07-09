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
        $today = Carbon::createFromFormat('Y-m-d', '2025-07-22')->startOfDay();

        $activePoints = UserActivePoint::firstOrNew(['user_id' => $userId]);

        $lastActiveDate = $activePoints->last_active_date
            ? Carbon::parse($activePoints->last_active_date)->startOfDay()
            : null;

        // ✅ Decide day_counter
        if (!$lastActiveDate) {
            $activePoints->day_counter = 1;
            $activePoints->first_active_date = $today;
        } else {
            $daysDiff = $today->diffInDays($lastActiveDate);

            if ($daysDiff === 0) {
                // Same day → kuch nahi
            } elseif ($daysDiff === 1) {
                $activePoints->day_counter += 1;
            } else {
                // Break → reset streak
                $activePoints->day_counter = 1;
                $activePoints->first_active_date = $today;

                // 👇 Calculate fresh day 1 points using your method
                $rule = LoginRewardRule::where('day', 1)->first();
                $rulePoints = $rule ? $rule->points : 0;
                $latestWithdraw = $this->getLatestWithdrawPoints($userId);

                $newPoints = $this->calculateNewActivePoints($rulePoints, 0, $latestWithdraw);

                $activePoints->current_points = $newPoints;
            }
        }

        $activePoints->last_active_date = $today;

        // ✅ Rule for current day_counter
        $rule = LoginRewardRule::where('day', $activePoints->day_counter)->first();
        $rulePoints = $rule ? $rule->points : 0;

        // ✅ Withdraw check
        $latestWithdraw = $this->getLatestWithdrawPoints($userId);

        // ✅ History exist check
        $existingHistory = UserActivePointsHistory::where([
            'user_id' => $userId,
            'source' => 'active_reward',
            'day_counter' => $activePoints->day_counter,
        ])->first();

        if ($existingHistory) {
            // 👇 Pehle hi mile hue → points assign nahi honge
            $newPoints = 0;

            $existingHistory->update([
                'remarks' => 'Day ' . $activePoints->day_counter . ' Reward',
            ]);
        } else {
            // 👇 Calculate fresh using your helper
            $newPoints = $this->calculateNewActivePoints(
                $rulePoints,
                $activePoints->current_points,
                $latestWithdraw
            );

            if ($newPoints > 0) {
               $finalPoints =  $activePoints->current_points += $newPoints;

                UserActivePointsHistory::create([
                    'user_id' => $userId,
                    'source' => 'active_reward',
                    'day_counter' => $activePoints->day_counter,
                    'points_awarded' => $finalPoints    ,
                    'remarks' => 'Day ' . $activePoints->day_counter . ' Reward',
                ]);

                $wallet = UserWallet::firstOrCreate(['user_id' => $userId]);
                $wallet->total_points += $newPoints;
                $wallet->save();
            }
        }

        $activePoints->save();
    });

    return response()->json(['status' => 'ok']);
}

private function getLatestWithdrawPoints($userId)
{
    return UserActivePointsHistory::where('user_id', $userId)
        ->where('source', 'withdraw')
        ->latest('created_at')
        ->value('points_awarded') ?? 0;
}




private function calculateNewActivePoints($rulePoints, $currentPoints, $latestWithdraw)
{
    $newPoints = 0;

    if ($latestWithdraw > 0) {
        $newPoints = $rulePoints - $latestWithdraw;
    } else {
        $newPoints = $rulePoints - $currentPoints;
        // $newPoints = $remain + $currentPoints;
    }

    if ($newPoints < 0) {
        $newPoints = 0;
    }

    return $newPoints;
}

}
