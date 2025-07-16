<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
    public function rank(Request $request)
    {
        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $type = $request->query('type'); // 'monthly' or 'yearly'
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $users = User::with(['sales' => function ($query) use ($type, $currentMonth, $currentYear) {
                if ($type === 'monthly') {
                    $query->whereMonth('created_at', $currentMonth)
                          ->whereYear('created_at', $currentYear);
                } elseif ($type === 'yearly') {
                    $query->whereYear('created_at', $currentYear);
                }
            }])->get();

            // Build ranking data
            $rankings = $users->map(function ($user) {
                return [
                    'user_id'        => $user->id,
                    'name'           => $user->name,
                    'products_count' => $user->sales->count(),
                    'points'         => $user->sales->sum('points_earned'),
                ];
            })
            ->sort(function ($a, $b) {
                if ($b['products_count'] === $a['products_count']) {
                    return $b['points'] <=> $a['points'];
                }
                return $b['products_count'] <=> $a['products_count'];
            })
            ->values();

            // Assign rank
            $rankings = $rankings->map(function ($user, $index) {
                $user['rank'] = $index + 1;
                return $user;
            });

            // Extract current user's ranking
            $currentUserRank = $rankings->firstWhere('user_id', $authUser->id);

            // Final response
            return response()->json([
                'message' => 'Rankings Fetched Successfully',
                'current_user' => $currentUserRank ?? [
                    'user_id'        => $authUser->id,
                    'name'           => $authUser->name,
                    'products_count' => 0,
                    'points'         => 0,
                    'rank'           => null
                ],
                'ranking_list' => $rankings
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in RankingController@rank: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed To Fetch Rankings',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}