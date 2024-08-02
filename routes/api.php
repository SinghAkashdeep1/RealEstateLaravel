<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\UserManagentController;


//Admin Pannel 
Route::post('/admin/login',[AdminAuthController::class, 'adminLoginSubmit']);

// User Pannel

Route::post('/registration',[AuthController::class, 'userRegister']);
Route::post('/login',[AuthController::class, 'userLoginSubmit']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['middleware' => ['role:Admin']], function () {
        Route::get('/user/list',[UserManagentController::class, 'index']);

    });

});