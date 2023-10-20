<?php

use App\Constants\UserConstant\UserRole;
use App\Http\Controllers\Api\AuthenController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthenController::class, 'login'])->name('login');
Route::post('/signup', [AuthenController::class, 'signup'])->name('auth.signup');
Route::get('/unauthenticated', [AuthenController::class, 'throwAuthenError'])->name('auth.authenError');
Route::get('/unauthorized', [AuthenController::class, 'throwAuthorError'])->name('auth.authorError');


Route::group([
    'middleware' => 'auth:api',
], function ($router) {
    Route::group([
        'middleware' => 'author:' . UserRole::ADMIN,
    ], function ($router) {
        Route::get('/users', [UserController::class, 'index']);
    });
    
    Route::controller(AuthenController::class)->group(function () {
        Route::name('auth.')->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/refresh', 'refresh')->name('refresh');
            Route::post('/change-pass', 'changePassWord')->name('changePassword');
            Route::get('/user-profile', 'getUserProfile')->name('getUserProfile');
        });
    });
});