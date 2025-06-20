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
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProductController;
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
| Here is where you can register web routes for your application. These
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

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('check.permission:Roles,view');

        Route::get('/roles-create', [RoleController::class, 'create'])->name('create.role')->middleware('check.permission:Roles,create');

        Route::post('/store-role', [RoleController::class, 'store'])->name('store.role')->middleware('check.permission:Roles,create');


        Route::get('/roles-permissions/{id}', [RoleController::class, 'permissions'])->name('role.permissions')->middleware('check.permission:Roles,edit');


        //////////////////////////////////////////
        Route::post('/admin/roles/{id}/permissions/store', [RoleController::class, 'storePermissions'])->name('roles.permissions.store')->middleware('check.permission:role,create');


        Route::delete('/delete-role/{id}', [RoleController::class, 'delete'])->name('delete.role')->middleware('check.permission:role,delete');

    

    // ############ Term & Condition #################
    Route::get('term-condition', [SecurityController::class, 'TermCondition']) ->middleware('check.permission:Terms & Conditions,view');
    Route::get('term-condition-edit', [SecurityController::class, 'TermConditionEdit']) ->middleware('check.permission:Terms & Conditions,edit');
    Route::post('term-condition-update', [SecurityController::class, 'TermConditionUpdate']) ->middleware('check.permission:Terms & Conditions
,edit');
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

    Route::get('/login-reward-rules', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'index'])->name('login-reward-rules.index')->middleware('check.permission:Login Rewards Points,view');

    Route::get('/login-reward-rules-create', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'create'])->name('login-reward-rules.create')->middleware('check.permission:Login Rewards Points,create');

    Route::post('/login-reward-rules-store', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'store'])->name('loginrewardrules.store')->middleware('check.permission:Login Rewards Points,create');

    Route::get('/login-reward-rules-edit/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'edit'])->name('login-reward-rules.edit')->middleware('check.permission:Login Rewards Points,edit');

    Route::post('/login-reward-rules-update/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'update'])->name('login-reward-rules.update')->middleware('check.permission:Login Rewards Points,edit');

    Route::delete('/login-reward-rules-destroy/{id}', [\App\Http\Controllers\Admin\LoginRewardRuleController::class, 'destroy'])->name('login-reward-rules.destroy')->middleware('check.permission:Login Rewards Points,delete');


    // ############ Products #################

    Route::get('/products', [ProductController::class, 'Index'])->name('product.index') ->middleware('check.permission:Products,view');
     Route::get('/products-create', [ProductController::class, 'create'])->name('product.create') ->middleware('check.permission:Products,create');
    Route::post('/products-store', [ProductController::class, 'store'])->name('product.store') ->middleware('check.permission:Products,create');
    Route::get('/products-edit/{id}', [ProductController::class, 'edit'])->name('product.edit') ->middleware('check.permission:Products,edit');
    Route::post('/products-update/{id}', [ProductController::class, 'update'])->name('product.update') ->middleware('check.permission:Products,edit');
    Route::delete('/products-destroy/{id}', [ProductController::class, 'delete'])->name('product.delete') ->middleware('check.permission:Products,delete');
    Route::get('/products-details/{id}', [ProductController::class, 'ProductDetails'])->name('product.detail') ->middleware('check.permission:Products,view');

    Route::get('/products-scancreate', [ProductController::class, 'ScanCreate'])->name('product.scancreate') ->middleware('check.permission:Products,create');

    Route::post('/products-scanstore', [ProductController::class, 'ScanStore'])->name('product.scanstore') ->middleware('check.permission:Products,create');
Route::post('admin/product-batch-store', [ProductController::class, 'storeBatch'])->name('product.batch.store')->middleware('check.permission:Products,create');
Route::delete('admin/product-batch-delete/{id}', [ProductController::class, 'deleteBatch'])->name('bactches.delete')->middleware('check.permission:Products,create');;

    // ############ Points Conversions #################

    Route::get('/point-conversions-index', [PointConversionController::class, 'index'])->name('point-conversions.index')->middleware('check.permission:Points Conversion,view');

    Route::get('/point-conversions-create', [PointConversionController::class, 'create'])->name('point-conversions.create')->middleware('check.permission:Points Conversion,create');

 Route::post('point-conversions-store', [PointConversionController::class, 'store'])->name('point-conversions.store')
        ->middleware('check.permission:Points Conversion,create');

    Route::get('/point-conversions-edit/{id}', [PointConversionController::class, 'edit'])->name('point-conversions.edit')->middleware('check.permission:Points Conversion,edit');

    Route::post('/point-conversions-update/{id}', [PointConversionController::class, 'update'])->name('point-conversions.update')->middleware('check.permission:Points Conversion,edit');

// ############ Withdraw Requests #################
Route::get('/withdrawrequest', [WithdrawRequestController::class, 'withdrawRequests'])->name('withdraw.requests');




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

        Route::post('/admin/subadmin/toggle-status', [SubAdminController::class, 'toggleStatus'])->name('admin.subadmin.toggleStatus');

    });

        // ############ Sales #################

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index')->middleware('check.permission:Sales,view');



    // ############ Notifications #################
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notification',  'index')->name('notification.index') ->middleware('check.permission:Notifications,view');
        Route::post('/notification-store',  'store')->name('notification.store') ->middleware('check.permission:Notifications,create');
        Route::delete('/notification-destroy/{id}',  'destroy')->name('notification.destroy') ->middleware('check.permission:Notifications,delete');
        Route::get('/get-users-by-type', 'getUsersByType');

    
    });

    // ############ Seo Routes #################

     Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
    Route::get('/seo/{id}/edit', [SeoController::class, 'edit'])->name('seo.edit');
    Route::post('/seo/{id}', [SeoController::class, 'update'])->name('seo.update');
    Route::get('/admin/seo/page/{id}', [SeoController::class, 'getPage'])->name('seo.page');


    // ############ Web Routes #################

         Route::get('/home-page', [WebController::class, 'homepage'])->name('web.homepage');
         Route::get('/about-page', [WebController::class, 'aboutpage'])->name('web.aboutpage');
         Route::get('/contact-page', [WebController::class, 'contactpage'])->name('web.contactpage');




    // ############ Contact Us #################
Route::get('/admin/contact-us', [ContactController::class, 'index'])->name('contact.index') ->middleware('check.permission:Contact us,view');
Route::get('/admin/contact-us-create', [ContactController::class, 'create'])->name('contact.create') ->middleware('check.permission:Contact us,create');
Route::post('/admin/contact-us-store', [ContactController::class, 'store'])->name('contact.store') ->middleware('check.permission:Contact us,create');
Route::get('/admin/contact-us-edit/{id}', [ContactController::class, 'updateview'])->name('contact.updateview') ->middleware('check.permission:Contact us,edit');
Route::post('/admin/contact-us-update/{id}', [ContactController::class, 'update'])->name('contact.update') ;



});
