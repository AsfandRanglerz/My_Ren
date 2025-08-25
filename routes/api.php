<?php




use App\Http\Controllers\Api\AllProductController;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\ContactUsController;

use App\Http\Controllers\Api\EmailOtpController;

use App\Http\Controllers\Api\ForgotPasswordController;

use App\Http\Controllers\Api\LoginController;

use App\Http\Controllers\Api\LoginRewardRuleController;

use App\Http\Controllers\Api\NotificationController;

use App\Http\Controllers\Api\ProductDetailController;

use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\SearchHistoryController;
use App\Http\Controllers\Api\UpdateProfileController;
use App\Http\Controllers\Api\UserActivePointsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRankingController;
use App\Http\Controllers\Api\VoucherDetailController;
use App\Http\Controllers\Api\WalletUserPointController;
use App\Http\Controllers\Api\WithdrawRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SideMenueController;
use App\Http\Controllers\SideMenuPermissionController;
use Illuminate\Support\Facades\Route;




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



     // User Active Points

     Route::post('/user-active-reward/{userId}', [UserActivePointsController::class, 'handleUserActiveReward']);



     //sales routes

Route::post('/sales-store', [SaleController::class, 'store'])

     ->name('sales.store');



     // User Registration


Route::post('/send-otp', [EmailOtpController::class, 'sendOtp']);
Route::post('/verify-otp', [EmailOtpController::class, 'verifyOtp']);
Route::post('/register-user', [EmailOtpController::class, 'registerUser']);

//get user profile
Route::middleware('auth:sanctum')->group(function () {
Route::get('/getprofiledetail', [UpdateProfileController::class, 'getProfile']);
Route::post('/updateprofiledetail', [UpdateProfileController::class, 'sendOtp']);
Route::post('/verifyprofiledetail', [UpdateProfileController::class, 'verifyOtp']);
Route::post('/update-profile', [EmailOtpController::class, 'requestUpdateOtp']);
Route::post('/update-profile-verify', [EmailOtpController::class, 'verifyAndUpdateContact']);
Route::get('/get-logged-in-user-info', [EmailOtpController::class, 'getLoggedInUserInfo']);

});

//user login

Route::post('/user-login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

//Forgot Password
Route::post('/forgotpassword', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/forgotverifyotp', [ForgotPasswordController::class, 'forgotverifyOtp']);
Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp']);
Route::post('/resetpassword', [ForgotPasswordController::class, 'resetPassword']);


//Notifications
Route::get('/notifications', [NotificationController::class, 'getUserNotifications'])->middleware('auth:sanctum');
Route::get('/notification/{id}', [NotificationController::class, 'showNotification'])->middleware('auth:sanctum');
Route::post('/clearnotification', [NotificationController::class, 'clearAll'])->middleware('auth:sanctum');

// User Withdraw Requests
Route::post('/withdraw-request', [WithdrawRequestController::class, 'store'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/userwithdrawdata', [WalletUserPointController::class, 'withdrawRequest']);

});


//Scan Code
Route::post('/scancode', [ScanController::class, 'storeScanCode'])->middleware('auth:sanctum');


// products details

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/productdetail', [ProductDetailController::class, 'getUserProductSales']);
    });

    
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales/products', [SaleController::class, 'getAllProducts']);
    });

// user spcific rankings

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/specific-userranking', [UserRankingController::class, 'monthlyRankings']);

    });



    // user total points

  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/userwallettotalpoints', [WalletUserPointController::class, 'getTotalPoints']);

    });



    //rankings of all users

    Route::get('/ranking',[RankingController::class, 'rank'])->middleware('auth:sanctum');

    // Reward Points

    Route::get('/loginrewardrules', [LoginRewardRuleController::class, 'index']);





    // wallet points 

 Route::middleware('auth:sanctum')->group(function () {
    Route::get('/userwalletpoints', [WalletUserPointController::class, 'getPoint']);

    });


// Withdraw Requests



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/userwalletpoints', [WalletUserPointController::class, 'getPoint']);
});

    // search history

    Route::middleware('auth:sanctum')->group(function () {
     Route::get('/getsearchhistory', [SearchHistoryController::class, 'getUserSoldProductNames']);
    Route::get('/searchhistory', [SearchHistoryController::class, 'index']);


    Route::get('/searchbyname', [SearchHistoryController::class, 'searchUserSalesByProductName']);
});



// voucher routes

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/voucherdetail/{id}', [VoucherDetailController::class, 'getVoucherDetail']);
    Route::get('/getvouchers', [VoucherDetailController::class, 'getVoucher']);

    });


    //





Route::middleware('auth:sanctum')->group(function () {


    // Contact Us
    Route::post('/contactus/{id}',[ContactUsController::class, 'contact'])->name('contactus');
Route::get('/getcontactus/{id}',[ContactUsController::class, 'getContact'])->name('getcontactus');
});