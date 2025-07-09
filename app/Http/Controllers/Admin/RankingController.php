<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class RankingController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'month'); 

        $rankings = User::with(['sales' => function ($query) use ($type) {
            $query->when($type === 'monthly', function ($q) {
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })
            ->when($type === 'yearly', function ($q) {
                $q->whereYear('created_at', now()->year);
            });
        }])
        ->get()
        ->map(function ($user) {
            $products_count = $user->sales->count(); 
            $points = $user->sales->sum('points_earned');

            return (object)[
                'user_id' => $user->id,
                'name' => $user->name,
                'products_count' => $products_count, 
                'points' => $points,
            ];
        })
        ->sort(function ($a, $b) {
            if ($b->products_count == $a->products_count) {
                return $b->points <=> $a->points;
            }
            return $b->products_count <=> $a->products_count;
        })
        ->values();

        return view('admin.ranking.index', [
            'rankings' => $rankings,
            'type' => $type
        ]);
    }

}

