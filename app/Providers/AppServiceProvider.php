<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRolePermission;
use App\Models\SubAdmin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */


    public function boot()
    {
        View::composer('*', function ($view) {
            $sideMenuPermissions = collect();
            $countClaimedVoucher = 0;
			$Deduction = 0;

            if (Auth::guard('subadmin')->check()) {
                $user = Auth::guard('subadmin')->user();

                // Load roles from pivot
                $role = $user->roles()->first(); // assumes 1 role per subadmin

                if ($role) {
                    $roleId = $role->id;

                    $sideMenuPermissions = UserRolePermission::with(['permission', 'sideMenue'])
                        ->where('role_id', $roleId)
                        ->get()
                        ->groupBy(function ($item) {
                            return $item->sideMenue->name ?? 'undefined';
                        })
                        ->map(function ($items) {
                            return $items->pluck('permission.name');
                        });
                }
            }

            // Claimed Vouchers count
            $countClaimedVoucher = \App\Models\ClaimVoucher::where('is_seen', 0)->count();
			$Deduction = \App\Models\TempPointDeductionHistory::where('status', 'pending')->count();

            // Pass variables to all views
            $view->with([
                'sideMenuPermissions' => $sideMenuPermissions,
                'countClaimedVoucher' => $countClaimedVoucher,
				'countPendingDeduction' => $Deduction,
            ]);
        });
    }
}
