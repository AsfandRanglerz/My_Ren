<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class UserRankingController extends Controller
{
    public function monthlyRankings(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get month and year from request or fallback to current month/year
            $month = $request->query('month', Carbon::now()->month);
            $year = $request->query('year', Carbon::now()->year);

            $users = User::with(['sales' => function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                      ->whereYear('created_at', $year);
            }])->get();

            // Create rankings from user sales
            $rankings = $users->map(function ($user) {
                return (object)[
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'total_scans' => $user->sales->count(),
                    'points' => $user->sales->sum('points_earned'),
                ];
            });

            // Sort rankings: scans descending, then points descending
            $sorted = $rankings->sort(function ($a, $b) {
                if ($b->total_scans === $a->total_scans) {
                    return $b->points <=> $a->points;
                }
                return $b->total_scans <=> $a->total_scans;
            })->values();

            // Find the authenticated user’s rank
            $userRank = null;
            $userStats = null;

            foreach ($sorted as $index => $row) {
                if ($row->user_id == $user->id) {
                    $userStats = $row;
                    $userRank = $index + 1;
                    break;
                }
            }

            return response()->json([
                
                'month' => (int) $month,
                'year' => (int) $year,
                'user_stats' => $userStats ?? [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'total_scans' => 0,
                    'points' => 0
                ],
                'user_rank' => $userRank,
                'top_user' => $sorted->first(),
            ]);

        } catch (Exception $e) {
            return response()->json([
                
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function SpecificmonthlyRankings(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Requested month/year (fallback current)
        $month = $request->query('month', Carbon::now()->month);
        $year  = $request->query('year', Carbon::now()->year);

        // Current month/year for top user
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        /** -----------------------------
         * 1. Fetch rankings for requested month/year
         * ----------------------------- */
        $users = User::with(['sales' => function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }])->get();

        $rankings = $users->map(function ($u) {
            return (object)[
                'user_id'     => $u->id,
                'name'        => $u->name,
                'total_scans' => $u->sales->count(),
                'points'      => $u->sales->sum('points_earned'),
            ];
        });

        $sorted = $rankings->sort(function ($a, $b) {
            if ($b->total_scans === $a->total_scans) {
                return $b->points <=> $a->points;
            }
            return $b->total_scans <=> $a->total_scans;
        })->values();

        // User rank in requested month/year
        $userRank = null;
        $userStats = null;

        foreach ($sorted as $index => $row) {
            if ($row->user_id == $user->id) {
                $userStats = $row;
                $userRank = $index + 1;
                break;
            }
        }

        /** -----------------------------
         * 2. Always fetch current month/year top user
         * ----------------------------- */
        $currentUsers = User::with(['sales' => function ($q) use ($currentMonth, $currentYear) {
            $q->whereMonth('created_at', $currentMonth)
              ->whereYear('created_at', $currentYear);
        }])->get();

        $currentRankings = $currentUsers->map(function ($u) {
            return (object)[
                'user_id'     => $u->id,
                'name'        => $u->name,
                'total_scans' => $u->sales->count(),
                'points'      => $u->sales->sum('points_earned'),
            ];
        });

        $currentTopUser = $currentRankings->sort(function ($a, $b) {
            if ($b->total_scans === $a->total_scans) {
                return $b->points <=> $a->points;
            }
            return $b->total_scans <=> $a->total_scans;
        })->first();

        /** -----------------------------
         * Response
         * ----------------------------- */
        return response()->json([
            'message' => "Rankings fetched successfully for {$month}-{$year}",

            // Requested month/year
            'requested_month' => (int) $month,
            'requested_year'  => (int) $year,
            'user_stats'      => $userStats ?? [
                'user_id' => $user->id,
                'name' => $user->name,
                'total_scans' => 0,
                'points' => 0
            ],
            'user_rank'       => $userRank,
            // Always include current top user
            'current_top_user' => $currentTopUser,
            'current_month'    => $currentMonth,
            'current_year'     => $currentYear,
        ]);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Something Went Wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


}