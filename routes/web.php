<?php



use App\Models\Role;

use App\Models\SideMenue;

use App\Models\Permission;

use App\Models\UserRolePermission;

use App\Models\SideMenuHasPermission;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;

use App\Http\Controllers\BlogController;

use App\Http\Controllers\RoleController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\Admin\FaqController;

use App\Http\Controllers\Admin\AuthController;

use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\SignupRewardSettingController;
use App\Http\Controllers\Admin\ContactController;

use App\Http\Controllers\Admin\ProductController;

use App\Http\Controllers\Admin\RankingController;

use App\Http\Controllers\Admin\VoucherController;

use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\PointConversionController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RolePermissionController;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes   your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

/*Admin routes

 * */



Route::get('/admin', [AuthController::class, 'getLoginPage']);

Route::post('/login', [AuthController::class, 'Login']);

Route::get('/admin-forgot-password', [AdminController::class, 'forgetPassword']);

Route::post('/admin-reset-password-link', [AdminController::class, 'adminResetPasswordLink']);

Route::get('/change_password/{id}', [AdminController::class, 'change_password']);

Route::post('/admin-reset-password', [AdminController::class, 'ResetPassword']);

    // ############ Web Pages #################

   Route::get('/terms-conditions', [WebController::class, 'termsConditionspage'])->name('terms.conditions');

   Route::get('/privacy-policy', [WebController::class, 'privacyPolicy'])->name('privacy.policy');

   Route::get('/contact-us', [WebController::class, 'contactuspage'])->name('contact.us');



Route::prefix('admin')->middleware(['admin', 'check.subadmin.status'])->group(function () {

    Route::get('dashboard', [AdminController::class, 'getdashboard'])->name('admin.dashboard');

    Route::get('profile', [AdminController::class, 'getProfile']);

    Route::post('update-profile', [AdminController::class, 'update_profile']);



    // ############ Privacy-policy #################

    Route::get('privacy-policy', [SecurityController::class, 'PrivacyPolicy'])->middleware('check.permission:Privacy & Policy,view');

    Route::get('privacy-policy-edit', [SecurityController::class, 'PrivacyPolicyEdit'])->middleware('check.permission:Privacy & Policy,edit');

    Route::post('privacy-policy-update', [SecurityController::class, 'PrivacyPolicyUpdate']) ->middleware('check.permission:Privacy & Policy,edit');

    Route::get('privacy-policy-view', [SecurityController::class, 'PrivacyPolicyView']) ->middleware('check.permission:Privacy & Policy,view');



    // ############ Role Permissions #################



    // Route::get('roles-permission', [RolePermissionController::class, 'index'])->name('role-permission')->middleware('check.permission:role,view');



            



            // ############ Roles #################



        Route::get('/roles', [RoleController::class, 'index'])->name('Roles.index')->middleware('check.permission:Roles,view');



        Route::get('/roles-create', [RoleController::class, 'create'])->name('create.role')->middleware('check.permission:Roles,create');



        Route::post('/store-role', [RoleController::class, 'store'])->name('store.role')->middleware('check.permission:Roles,create');





        Route::get('/roles-permissions/{id}', [RoleController::class, 'permissions'])->name('role.permissions')->middleware('check.permission:Roles,view');





        //////////////////////////////////////////

        Route::post('/admin/roles/{id}/permissions/store', [RoleController::class, 'storePermissions'])->name('roles.permissions.store')->middleware('check.permission:Roles,create');





        Route::delete('/delete-role/{id}', [RoleController::class, 'delete'])->name('delete.role')->middleware('check.permission:Roles,delete');



    



    // ############ Term & Condition #################

    Route::get('term-condition', [SecurityController::class, 'TermCondition']) ->middleware('check.permission:Terms & Conditions,view');

    Route::get('term-condition-edit', [SecurityController::class, 'TermConditionEdit']) ->middleware('check.permission:Terms & Conditions,edit');

    Route::post('term-condition-update', [SecurityController::class, 'TermConditionUpdate']) ;

    Route::get('term-condition-view', [SecurityController::class, 'TermConditionView']) ->middleware('check.permission:Terms & Conditions

,view');



    // ############ About Us #################

    Route::get('about-us', [SecurityController::class, 'AboutUs']) ->middleware('check.permission:About us,view');

    Route::get('about-us-edit', [SecurityController::class, 'AboutUsEdit']) ->middleware('check.permission:About us,edit');

    Route::post('about-us-update', [SecurityController::class, 'AboutUsUpdate']) ->middleware('check.permission:About us,edit');

    Route::get('about-us-view', [SecurityController::class, 'AboutUsView']) ->middleware('check.permission:About us,view');



    Route::get('logout', [AdminController::class, 'logout']);



        // ############ Faq #################

    Route::get('faq-index', [FaqController::class, 'Faq'])->middleware('check.permission:Faqs,view');

    Route::get('faq-edit/{id}', [FaqController::class, 'FaqsEdit'])->name('faq.edit') ->middleware('check.permission:Faqs,edit');

    Route::post('faq-update/{id}', [FaqController::class, 'FaqsUpdate'])->middleware('check.permission:Faqs,edit');

    Route::get('faq-view', [FaqController::class, 'FaqView']) ->middleware('check.permission:Faqs,view');

    Route::get('faq-create', [FaqController::class, 'Faqscreateview']) ->middleware('check.permission:Faqs,create');

    Route::post('faq-store', [FaqController::class, 'Faqsstore']) ->middleware('check.permission:Faqs,create');

    Route::delete('faq-destroy/{id}', [FaqController::class, 'faqdelete'])->name('faq.destroy') ->middleware('check.permission:Faqs,delete');

    Route::post('/faqs/reorder', [FaqController::class, 'reorder'])->name('faq.reorder');



    // ############ Users #################



    Route::get('/user', [UserController::class, 'Index'])->name('user.index') ->middleware('check.permission:Users,view');

Route::get('/user-create', [UserController::class, 'createview'])->name('user.createview') ->middleware('check.permission:Users,create');

Route::post('/user-store', [UserController::class, 'create'])->name('user.create') ->middleware('check.permission:Users,create');

Route::get('/user-edit/{id}', [UserController::class, 'edit'])->name('user.edit') ->middleware('check.permission:Users,edit');

Route::post('/user-update/{id}', [UserController::class, 'update'])->name('user.update') ->middleware('check.permission:Users,edit');

Route::delete('/users-destory/{id}', [UserController::class, 'delete'])->name('user.delete') ->middleware('check.permission:Users,delete');

// Route::get('/users/trashed', [UserController::class, 'trashed']);

// Route::post('/users/{id}/restore', [UserController::class, 'restore']);

Route::delete('/users/{id}/force', [UserController::class, 'forceDelete'])->name('user.forceDelete') ->middleware('check.permission:Users,delete');



Route::post('/users/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');



    Route::get('/user-sales-details/{id}', [UserController::class, 'sales'])->name('user.saledetails') ->middleware('check.permission:Users,view');





        // ############ Login Rewards Points #################



    Route::get('/login-reward-rules', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'index'])->name('login-reward-rules.index')->middleware('check.permission:Reward Settings,view');



    Route::get('/login-reward-rules-create', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'create'])->name('login-reward-rules.create')->middleware('check.permission:Reward Settings,create');



    Route::post('/login-reward-rules-store', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'store'])->name('loginrewardrules.store')->middleware('check.permission:Reward Settings,create');



    Route::get('/login-reward-rules-edit/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'edit'])->name('login-reward-rules.edit')->middleware('check.permission:Reward Settings,edit');



    Route::post('/login-reward-rules-update/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'update'])->name('login-reward-rules.update');



    Route::delete('/login-reward-rules-destroy/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'destroy'])->name('login-reward-rules.destroy')->middleware('check.permission:Reward Settings,delete');





    // ############ Products #################



    Route::get('/devices', [ProductController::class, 'Index'])->name('product.index') ->middleware('check.permission:Devices/Products,view');

     Route::get('/devices-create', [ProductController::class, 'create'])->name('product.create') ->middleware('check.permission:Devices/Products,create');

    Route::post('/devices-store', [ProductController::class, 'store'])->name('product.store') ->middleware('check.permission:Devices/Products,create');

    Route::get('/devices-edit/{id}', [ProductController::class, 'edit'])->name('product.edit') ->middleware('check.permission:Devices/Products,edit');

    Route::post('/devices-update/{id}', [ProductController::class, 'update'])->name('product.update') ->middleware('check.permission:Devices/Products,edit');

    Route::delete('/devices-destroy/{id}', [ProductController::class, 'delete'])->name('product.delete') ->middleware('check.permission:Devices/Products,delete');

    Route::get('/devices-details/{id}', [ProductController::class, 'ProductDetails'])->name('product.detail') ->middleware('check.permission:Devices/Products,view');



    Route::get('/devices-scancreate', [ProductController::class, 'ScanCreate'])->name('product.scancreate') ->middleware('check.permission:Devices/Products,create');



    Route::post('/devices-scanstore', [ProductController::class, 'ScanStore'])->name('product.scanstore') ->middleware('check.permission:Devices/Products,create');

Route::post('admin/device-batch-store', [ProductController::class, 'storeBatch'])->name('product.batch.store')->middleware('check.permission:Devices/Products,create');

Route::delete('admin/device-batch-delete/{id}', [ProductController::class, 'deleteBatch'])->name('bactches.delete')->middleware('check.permission:Devices/Products,create');;



    Route::get('/devices-createdetails/{id}', [ProductController::class, 'CreateProductDetails'])->name('product.createdetails')->middleware('check.permission:Devices/Products,edit');



    // ############ Points Conversions #################

Route::get('/signup-reward-setting', [SignupRewardSettingController::class, 'index'])->name('signup_reward_setting.index');
    Route::post('signup-reward-setting', [SignupRewardSettingController::class, 'store'])->name('signup_reward_setting.store');
    Route::get('signup-reward-setting/{id}/edit', [SignupRewardSettingController::class, 'edit'])->name('signup_reward_setting.edit');
    Route::post('signup-reward-setting/{id}', [SignupRewardSettingController::class, 'update'])->name('signup_reward_setting.update');
    
    // ############ Withdraw Requests #################
    Route::get('/withdrawrequest/count', [WithdrawRequestController::class, 'withdrawalCounter'])->name('withdraw.counter');
    Route::get('/withdrawrequest', [WithdrawRequestController::class, 'index'])->name('withdraw.requests')->middleware('check.permission:Withdrawal Requests,view');
    Route::put('/withdrawrequest/{id}', [WithdrawRequestController::class, 'update'])->name('withdrawRequest.update');
    Route::delete('/withdrawrequest/{id}', [WithdrawRequestController::class, 'delete'])->name('withdrawRequest.delete');



    // ############ Sub Admin #################

    Route::controller(SubAdminController::class)->group(function () {

        Route::get('/subadmin',  'index')->name('subadmin.index') ->middleware('check.permission:Sub Admins,view');

        Route::get('/subadmin-create',  'create')->name('subadmin.create') ->middleware('check.permission:Sub Admins,create');

        Route::post('/subadmin-store',  'store')->name('subadmin.store') ->middleware('check.permission:Sub Admins,create');

        Route::get('/subadmin-edit/{id}',  'edit')->name('subadmin.edit') ->middleware('check.permission:Sub Admins,edit');

        Route::post('/subadmin-update/{id}',  'update')->name('subadmin.update') ->middleware('check.permission:Sub Admins,edit');

        Route::delete('/subadmin-destroy/{id}',  'destroy')->name('subadmin.destroy') ->middleware('check.permission:Sub Admins,delete');



        Route::post('/update-permissions/{id}', 'updatePermissions')->name('update.permissions');



        Route::post('/subadmin-StatusChange', 'StatusChange')->name('subadmin.StatusChange')->middleware('check.permission:Sub Admins,edit');



           Route::post('/toggle-status', [SubAdminController::class, 'toggleStatus'])->name('admin.subadmin.toggleStatus');



    });



        // ############ Sales #################



    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index')->middleware('check.permission:Sales,view');







    // ############ Notifications #################

    Route::controller(NotificationController::class)->group(function () {

        Route::get('/notification',  'index')->name('notification.index') ->middleware('check.permission:Notifications,view');

        Route::post('/notification-store',  'store')->name('notification.store')->middleware('check.permission:Notifications,create');

        Route::delete('/notification-destroy/{id}',  'destroy')->name('notification.destroy') ->middleware('check.permission:Notifications,delete');
        Route::delete('/notifications/delete-all', 'deleteAll')->name('notifications.deleteAll');
        Route::get('/get-users-by-type', 'getUsersByType');



    

    });



    // ############ Voucher Routes #################

Route::get('/voucher-index', [VoucherController::class, 'index'])->name('voucher.index') ->middleware('check.permission:Voucher Settings,view');

Route::get('/voucher-create', [VoucherController::class, 'create'])
    ->name('voucher.create')
    ->middleware('check.permission:Voucher Settings,create');


Route::post('/voucher-store', [VoucherController::class, 'store'])->name('voucher.store') ->middleware('check.permission:Voucher Settings,create');

Route::get('/voucher-edit/{id}', [VoucherController::class, 'edit'])->name('voucher.edit') ->middleware('check.permission:Voucher Settings,edit');

Route::post('admin/voucher-update/{id}', [VoucherController::class, 'update'])->name('voucher.update') ->middleware('check.permission:Voucher Settings,edit');

Route::delete('voucher-destroy/{id}', [VoucherController::class, 'destroy'])->name('voucher.destroy') ->middleware('check.permission:Voucher Settings,delete');

Route::get('/claimed-vocher', [VoucherController::class, 'ClaimVoucher'])->name('voucher.claimed') ->middleware('check.permission:Generated Coupons,view');
    // ############ Rankings Routes #################

Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index')->middleware('check.permission:Voucher Settings,view');




    // ############ Contact Us #################

Route::get('/admin/contact-us', [ContactController::class, 'index'])->name('contact.index') ->middleware('check.permission:Contact us,view');

Route::get('/admin/contact-us-create', [ContactController::class, 'create'])->name('contact.create') ->middleware('check.permission:Contact us,create');

Route::post('/admin/contact-us-store', [ContactController::class, 'store'])->name('contact.store') ->middleware('check.permission:Contact us,create');

Route::get('/admin/contact-us-edit/{id}', [ContactController::class, 'updateview'])->name('contact.updateview') ->middleware('check.permission:Contact us,edit');

Route::post('/admin/contact-us-update/{id}', [ContactController::class, 'update'])->name('contact.update') ;







});