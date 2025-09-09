<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstallReward;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallRewardController extends Controller
{
    //

 public function index()
{
    try {
        $user = Auth::id();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // agar relation ka naam 'sales' hai to ok, warna yahan relation ka name change karna parega
       $totalInstalls = Sale::where('user_id', $user)->count();

        $data = InstallReward::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'target_installs' => $item->target_sales, // 👈 rename
                'points' => $item->points,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Rewards fetched successfully',
            'data' => $data,
            'total_installs' => $totalInstalls
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
