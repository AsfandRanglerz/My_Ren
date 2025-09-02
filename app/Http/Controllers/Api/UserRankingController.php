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
                    'points' => 0,
                    'user_rank' => $userRank,
                ],
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
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Params
            $month = (int) $request->query('month', Carbon::now()->month);
            $year  = (int) $request->query('year',  Carbon::now()->year);
            $day   = $request->query('day');   // optional: 1..31
            $date  = $request->query('date');  // optional: YYYY-MM-DD
            $isTotal = filter_var($request->query('total', false), FILTER_VALIDATE_BOOLEAN);

            // Build requested range (for ranking)
            if ($date) {
                $start = Carbon::parse($date)->startOfDay();
                $end   = Carbon::parse($date)->endOfDay();
            } elseif ($day) {
                $start = Carbon::createFromDate($year, $month, (int)$day)->startOfDay();
                $end   = Carbon::createFromDate($year, $month, (int)$day)->endOfDay();
            } else {
                $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
            }

            // Current month/year (for current_top_user)
            $currentMonth = Carbon::now()->month;
            $currentYear  = Carbon::now()->year;

            /** -----------------------------
             * 1) Rankings for requested range
             * ----------------------------- */
            $users = User::with(['sales' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])->get();

            $rankings = $users->map(function ($u) {
                $totalScans = $u->sales->count();
                $points     = (int) $u->sales->sum('points_earned');
                return (object)[
                    'user_id'     => $u->id,
                    'name'        => $u->name,
                    'total_scans' => $totalScans,
                    'points'      => $points,
                ];
            });

            $sorted = $rankings->sort(function ($a, $b) {
                // Top priority: points
                if ($b->points === $a->points) {
                    // If points equal, compare scan count
                    return $b->total_scans <=> $a->total_scans;
                }
                return $b->points <=> $a->points;
            })->values();

            $userRank = null;
            $userStats = null;
            foreach ($sorted as $index => $row) {
                if ((int)$row->user_id === (int)$user->id) {
                    $userStats = $row;
                    $userRank  = $index + 1;
                    break;
                }
            }

            if (!$userStats) {
                $userStats = (object)[
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'total_scans' => 0,
                    'points' => 0,
                    'user_rank'  => $userRank,

                ];
            }
            $userStats->user_rank = $userRank;


            /** -----------------------------
             * 2) Current month top user (always)
             * ----------------------------- */
            $currentStart = Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfDay();
            $currentEnd   = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->endOfDay();

            // $currentUsers = User::with(['sales' => function ($q) use ($currentStart, $currentEnd) {
            //     $q->whereBetween('created_at', [$currentStart, $currentEnd]);
            // }])->get();

            $currentRankings = User::select('id', 'name')
                ->withCount(['sales as total_scans' => function ($q) use ($currentStart, $currentEnd) {
                    $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                }])
                ->withSum(['sales as total_points' => function ($q) use ($currentStart, $currentEnd) {
                    $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                }], 'points_earned')
                ->get()
                ->map(function ($u) {
                    return (object)[
                        'user_id' => $u->id,
                        'name' => $u->name,
                        'total_scans' => (int) $u->total_scans,    // correctly counts scan_code rows
                        'points' => (int) $u->total_points,        // correctly sums points_earned
                    ];
                });

            // Sort to get current top user
            $currentTopUser = $currentRankings->sort(function ($a, $b) {
                if ($b->points === $a->points) {
                    return $b->total_scans <=> $a->total_scans;
                }
                return $b->points <=> $a->points;
            })->values()->first();


            // return $currentTopUser;
            /** -----------------------------
             * 3) Total since join (if requested)
             * ----------------------------- */
            $userTotalsSinceJoin = null;
            if ($isTotal) {
                $userSales = \App\Models\Sale::where('user_id', $user->id)->get();
                $userTotalsSinceJoin = [
                    'total_scans'     => $userSales->count(),
                    'total_points'    => (int) $userSales->sum('points_earned'),
                    'unique_products' => $userSales->unique('product_id')->count(),
                    'since'           => $user->created_at ? $user->created_at->toDateTimeString() : null,
                ];
            }

            /** -----------------------------
             * Response
             * ----------------------------- */
            return response()->json([
                'message'         => 'Rankings fetched successfully',
                'requested_day'   => $day ? (int)$day : null,
                'requested_month' => $month,
                'requested_year'  => $year,
                'requested_date'  => $date ?: null,
                'is_total'        => $isTotal,
                'user_totals_since_join' => $userTotalsSinceJoin,
                'user_stats'      => $userStats,
                'current_top_user' => $currentTopUser ?: null,
                'current_month'   => $currentMonth,
                'current_year'    => $currentYear,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Something Went Wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
