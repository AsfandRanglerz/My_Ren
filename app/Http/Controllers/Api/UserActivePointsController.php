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


//     public function handleUserActiveReward($userId)
// {
//     DB::transaction(function () use ($userId) {

//         $today = Carbon::createFromFormat('Y-m-d', '2025-07-24')->startOfDay(); // 
//         $activePoints = UserActivePoint::firstOrNew(['user_id' => $userId]);

//         $lastActiveDate = $activePoints->last_active_date
//             ? Carbon::parse($activePoints->last_active_date)->startOfDay()
//             : null;

//         $latestWithdraw = $this->getLatestWithdrawPoints($userId);

//         if (!$lastActiveDate) {
//             // 👉 Pehli dafa
//             $activePoints->day_counter = 1;
//             $activePoints->first_active_date = $today;
//         } else {
//             $daysDiff = $today->diffInDays($lastActiveDate);

//             if ($daysDiff === 0) {
//                 // 👉 Same day → kuch nahi
//             } elseif ($daysDiff === 1) {
//                 // 👉 Streak continue → next day
//                 $activePoints->day_counter += 1;
//             } else {
//                 // 👉 Streak break → reset
//                 $activePoints->day_counter = 1;
//                 $activePoints->first_active_date = $today;
//                 $activePoints->current_points = 0; // 
//             }
//         }

//         $activePoints->last_active_date = $today;

//         // Ab rule lelo
//         $rule = LoginRewardRule::where('day', $activePoints->day_counter)->first();
//         $rulePoints = $rule ? $rule->points : 0;

//         // ✅ Check: pehle se history hai ya nahi
//         $existingHistory = UserActivePointsHistory::where([
//             'user_id' => $userId,
//             'source' => 'active_reward',
//             'day_counter' => $activePoints->day_counter,
//         ])->whereDate('created_at', $today)->first();

//         if ($existingHistory) {
//             // 👉 Already mil chuke
//             $existingHistory->update([
//                 'remarks' => 'Day ' . $activePoints->day_counter . ' Reward (Already Claimed)',
//             ]);
//         } else {
//             // 👉 Pehli dafa iss din ka reward
//             $newPoints = $this->calculateNewActivePoints(
//                 $rulePoints,
//                 $activePoints->current_points,
//                 $latestWithdraw
//             );

//             if ($newPoints > 0) {
//                $totalPoints = $activePoints->current_points += $newPoints;

//                 UserActivePointsHistory::create([
//                     'user_id' => $userId,
//                     'source' => 'active_reward',
//                     'day_counter' => $activePoints->day_counter,
//                     'points_awarded' => $totalPoints,
//                     'remarks' => 'Day ' . $activePoints->day_counter . ' Reward',
//                 ]);

//                 $wallet = UserWallet::firstOrCreate(['user_id' => $userId]);
//                 $wallet->total_points += $newPoints;
//                 $wallet->save();
//             }
//         }

//         $activePoints->save();
//     });

//     return response()->json(['status' => 'ok']);
// }

    //

    public function handleUserActiveReward($userId)
    {
        DB::transaction(function () use ($userId) {
            $today = Carbon::createFromFormat('Y-m-d', '2025-08-01');

        $activePoints = UserActivePoint::firstOrNew(['user_id' => $userId]);

        $lastActiveDate = $activePoints->last_active_date
            ? Carbon::parse($activePoints->last_active_date)->startOfDay()
            : null;

        $latestWithdraw = $this->getLatestWithdrawPoints($userId);

        if (!$lastActiveDate) {
            // First time
            $activePoints->day_counter = 1;
            $activePoints->first_active_date = $today;
        } else {
            $daysDiff = $today->diffInDays($lastActiveDate);

            if ($daysDiff === 0) {
    // 👉 Same day — Already marked active
    $existingHistory = UserActivePointsHistory::where([
        'user_id' => $userId,
        'source' => 'active_reward',
        'day_counter' => $activePoints->day_counter,
    ])->whereDate('created_at', $today)->first();

    return $existingHistory;

    if ($existingHistory) {
        // Already claimed, don't touch again
        return;
    }

    // Agar same day hai aur reward mila nahi tha, toh continue with rest of logic
} elseif ($daysDiff === 1) {
                // Streak continues
                $activePoints->day_counter += 1;
            } else {
                // Streak reset
                $activePoints->day_counter = 1;
                $activePoints->first_active_date = $today;
                $activePoints->current_points = 0;
            }
        }

        $activePoints->last_active_date = $today;

        $rule = LoginRewardRule::where('day', $activePoints->day_counter)->first();
        $rulePoints = $rule ? $rule->points : 0;

        // Fetch wallet to check current total_points
        $wallet = UserWallet::firstOrCreate(['user_id' => $userId]);

        // Check history already exists
        $existingHistory = UserActivePointsHistory::where([
            'user_id' => $userId,
            'source' => 'active_reward',
            'day_counter' => $activePoints->day_counter,
        ])->whereDate('created_at', $today)->first();

        if ($existingHistory) {
            // Already claimed today
            $existingHistory->update([
                'remarks' => 'Day ' . $activePoints->day_counter . ' Reward (Already Claimed)',
            ]);
        } else {
            $newPoints = $this->calculateNewActivePoints(
                $rulePoints,
                $activePoints->current_points,
                $wallet->total_points === 0 ? $latestWithdraw : $activePoints->current_points
            );

            if ($newPoints > 0) {
                $activePoints->current_points += $newPoints;
                $wallet->total_points += $newPoints;

                // Conditionally update or create
                $existingEntry = UserActivePointsHistory::where([
                    'user_id' => $userId,
                    'source' => 'active_reward',
                    'day_counter' => $activePoints->day_counter,
                ])->whereDate('created_at', $today)->first();

                if ($existingEntry) {
                    $existingEntry->update([
                        'points_awarded' => $activePoints->current_points,
                        'remarks' => 'Day ' . $activePoints->day_counter . ' Reward (Updated)',
                    ]);
                } else {
                    UserActivePointsHistory::create([
                        'user_id' => $userId,
                        'source' => 'active_reward',
                        'day_counter' => $activePoints->day_counter,
                        'points_awarded' => $activePoints->current_points,
                        'remarks' => 'Day ' . $activePoints->day_counter . ' Reward',
                    ]);
                }

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