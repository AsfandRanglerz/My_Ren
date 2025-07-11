<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\SaleController;

use App\Http\Controllers\Api\ScanController;

use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Admin\SeoController;

use App\Http\Controllers\Api\LoginController;

use App\Http\Controllers\SideMenueController;

use App\Http\Controllers\PermissionController;

use App\Http\Controllers\Api\EmailOtpController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\SideMenuPermissionController;
use App\Http\Controllers\Api\UserActivePointsController;



/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

//     return $request->user();

// });



Route::post('/roles', [RoleController::class, 'store']);



Route::post('/permissions', [PermissionController::class, 'store']);

Route::post('/sidemenue', [SideMenueController::class, 'store']);



Route::post('/permission-insert', [SideMenuPermissionController::class, 'assignPermissions']);



// seo routes

Route::post('/seo-bulk', [SeoController::class, 'storeBulk'])

     ->name('seo.bulk-update');


     // User Active Points

     Route::post('/user-active-reward/{userId}', [UserActivePointsController::class, 'handleUserActiveReward']);



     //sales routes

Route::post('/sales-store', [SaleController::class, 'store'])

     ->name('sales.store');



     // User Registration

     Route::post('/send-otp', [EmailOtpController::class, 'sendOtp']);

Route::post('/verify-otp', [EmailOtpController::class, 'verifyOtp']);

Route::post('/userregistercomplete', [UserController::class, 'completeRegistration']);

//user login

Route::post('/userlogin', [LoginController::class, 'login'])->name('user.login');

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');


//Notifications
Route::get('/notifications', [NotificationController::class, 'getUserNotifications'])->middleware('auth:sanctum');
Route::get('/notification/{id}', [NotificationController::class, 'showNotification'])->middleware('auth:sanctum');
Route::post('/clearnotification', [NotificationController::class, 'clearAll'])->middleware('auth:sanctum');


//Scan Code
Route::post('/scancode', [ScanController::class, 'storeScanCode'])->middleware('auth:sanctum');








Route::middleware('auth:sanctum')->group(function () {

    Route::get('get-profile', [AuthController::class, 'getProfile']); // Get Profile

    Route::put('update-profile', [AuthController::class, 'updateProfile']); // Update Profile



    // Password reset for Admin & SubAdmin via API

    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

    Route::get('/verify-reset-token/{token}', [AuthController::class, 'verifyResetToken']);

    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

});



