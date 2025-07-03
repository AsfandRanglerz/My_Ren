<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class RankingController extends Controller
{
    public function index()
    {
        $rankings = User::with('sales')
            ->get()
            ->map(function ($user) {
                $uniqueProducts = $user->sales->pluck('product_id')->unique()->count();
                $totalPoints = $user->sales->sum('points_earned');

                return (object)[
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'products_count' => $uniqueProducts,
                    'points' => $totalPoints,
                ];
            })
            
            ->sortByDesc(function ($user) {
                return $user->points; 
            })
            ->sortByDesc(function ($user) {
                return $user->products_count; 
            })
            ->values();  

        return view('admin.ranking.index', ['rankings' => $rankings]);
    }
}

