<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\UserManagentController;
use App\Http\Controllers\Api\Admin\AdminForgotPasswordController;
use App\Http\Controllers\Api\User\ForgotPasswordController;
use App\Http\Controllers\Api\Admin\AdminProfileController;
use App\Http\Controllers\Api\Admin\ListingTypeController;
use App\Http\Controllers\Api\Admin\PropertyTypeController;
use App\Http\Controllers\Api\Admin\PropertySubTypeController;
use App\Http\Controllers\Api\User\PropertyController;
use App\Http\Controllers\Api\Admin\PropertyAdminController;
use App\Http\Controllers\Api\User\SearchController;
use App\Http\Controllers\Api\User\UserAuthController;


//Admin Pannel 
Route::post('admin/login',[AdminAuthController::class, 'adminLoginSubmit']);
Route::post('admin/forgot-password', [AdminForgotPasswordController::class, 'submitAdminForgetPasswordForm']);
Route::get('admin/reset-password/{token}', [AdminForgotPasswordController::class, 'showAdminResetPasswordForm']);
Route::post('admin/reset-password', [AdminForgotPasswordController::class, 'submitAdminResetPasswordForm']);



Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['middleware' => ['role:Admin']], function () {
        Route::post('admin/logout',[AdminAuthController::class, 'destroy']);
        
        //admin profile
        Route::get('admin/profile',[AdminProfileController::class, 'adminprofile']);
        Route::post('admin/profile/edit/{id}',[AdminprofileController::class, 'update']);
        Route::get('admin/profile/change_password',[AdminprofileController::class, 'changePassword']);
        Route::post('admin/profile/change_password',[AdminprofileController::class, 'changePasswordSave']);
  
        // User management
        Route::get('admin/user/list',[UserManagentController::class, 'index']);
        Route::get('admin/user/add', [UserManagentController::class, 'create']);
        Route::post('admin/user/add', [UserManagentController::class, 'store']);
        Route::get('admin/user/status/{id}',[UserManagentController::class, 'status']);
        Route::get('admin/user/edit/{id}',[UserManagentController::class, 'show']);
        Route::post('admin/user/update/{id}', [UserManagentController::class, 'update']);
        Route::delete('admin/user/delete/{id}', [UserManagentController::class, 'delete']);

        //Listing Type
        Route::get('admin/listing-type/list',[ListingTypeController::class, 'index']);
        Route::post('admin/listing-type/add',[ListingTypeController::class, 'store']);
        Route::get('admin/listing-type/status/{id}',[ListingTypeController::class, 'status']);
        Route::delete('admin/listing-type/delete/{id}', [ListingTypeController::class, 'delete']);

        //Property Type
        Route::get('admin/property-type/list',[PropertyTypeController::class, 'index']);
        Route::post('admin/property-type/add',[PropertyTypeController::class, 'store']);
        Route::get('admin/property-type/status/{id}',[PropertyTypeController::class, 'status']);
        Route::delete('admin/property-type/delete/{id}', [PropertyTypeController::class, 'delete']);
   
        //Property Sub Type
        Route::get('admin/property-subtype/list',[PropertySubTypeController::class, 'index']);
        Route::post('admin/property-subtype/add',[PropertySubTypeController::class, 'store']);
        Route::get('admin/property-subtype/status/{id}',[PropertySubTypeController::class, 'status']);
        Route::delete('admin/property-subtype/delete/{id}', [PropertySubTypeController::class, 'delete']);

        //Property
        Route::get('admin/property/list',[PropertyAdminController::class, 'index']);
        Route::get('admin/property/{id}',[PropertyAdminController::class, 'show']);
        Route::get('admin/property/status/{id}',[PropertyAdminController::class, 'status']);


    });

});


// User Pannel

Route::post('/registration',[AuthController::class, 'userRegister']);
Route::post('/login',[AuthController::class, 'userLoginSubmit']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm']);
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm']);
Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm']);

Route::get('/type/property',[PropertyController::class, 'index']);
Route::get('/list/properties',[PropertyController::class, 'propertyList']);
Route::get('/property/detail/{id}',[PropertyAdminController::class, 'show']);
Route::get('/search/property-types',[PropertyController::class, 'propertyTypes']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['middleware' => ['role:User']], function () {
        Route::post('/logout',[AuthController::class, 'destroy']);
      
        Route::post('/add/property',[PropertyController::class, 'store']);
       
    });
});