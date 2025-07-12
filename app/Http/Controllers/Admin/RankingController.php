<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
{
    $type = $request->query('type', 'overall'); // Default: overall
    $selectedMonth = $request->query('month'); // for month-based filtering
    $currentYear = now()->year;
    $currentMonth = now()->month;

    $users = User::with('sales')->get();

    $rankedUsers = [];
    $unrankedUsers = [];

    foreach ($users as $user) {
        $filteredSales = $user->sales->filter(function ($sale) use ($type, $currentYear, $currentMonth, $selectedMonth) {
            if ($type === 'month') {
                return $sale->created_at->year == $currentYear &&
                       $sale->created_at->month == ($selectedMonth ?? $currentMonth);
            } elseif ($type === 'year') {
                return $sale->created_at->year == $currentYear;
            } else {
                return true; // Overall
            }
        });

        $periodProducts = $filteredSales->count();
        $periodPoints = $filteredSales->sum('points_earned');

        $totalProducts = $user->sales->count();
        $totalPoints = $user->sales->sum('points_earned');

        $userData = (object)[
            'user_id' => $user->id,
            'name' => $user->name,
            'products_count' => $periodProducts,
            'points' => $periodPoints,
            'lifetime_products' => $totalProducts,
            'lifetime_points' => $totalPoints,
        ];

        if ($periodProducts > 0) {
            $rankedUsers[] = $userData;
        } else {
            $unrankedUsers[] = $userData;
        }
    }

    $sorted = collect($rankedUsers)->sort(function ($a, $b) {
        return $b->products_count === $a->products_count
            ? $b->points <=> $a->points
            : $b->products_count <=> $a->products_count;
    })->values();

    $finalRanking = $sorted->merge($unrankedUsers);

    return view('admin.ranking.index', [
        'rankings' => $finalRanking,
        'type' => $type,
        'selectedMonth' => $selectedMonth,
    ]);
}

}