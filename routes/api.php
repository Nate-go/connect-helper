<?php

use App\Constants\UserConstant\UserRole;
use App\Http\Controllers\Api\AuthenController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\TagController;
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
Route::post('/send-verify', [AuthenController::class, 'sendVerify'])->name('sendVerify');
Route::post('/active-account', [AuthenController::class, 'activeAccount'])->name('activeAccount');

Route::group(['prefix' => 'auth'], function () {
    Route::get('/google', [AuthenController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthenController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:api')->group(function() {
    Route::middleware('author:' . UserRole::ADMIN)->group(function () {
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::get('/', [UserController::class,'index'])->name('getAllUser');
            
        });
    });
    
    Route::controller(AuthenController::class)->group(function () {
        Route::name('auth.')->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/refresh', 'refresh')->name('refresh');
            Route::post('/reset-pass', 'resetPassWord')->name('resetPassword');
            Route::get('/user-profile', 'getUserProfile')->name('getUserProfile');
        });
    });

    Route::controller(ConnectionController::class)->group(function () {
        Route::name('connection.')->group(function () {
            Route::get('/connections', 'index')->name('getConnection');
        });
    });

    Route::controller(TagController::class)->group(function () {
        Route::name('tag.')->group(function () {
            Route::get('/tags', 'index')->name('get');
            Route::post('/tags', 'store')->name('store');
            Route::put('/tags/{tag_id}', 'update')->name('update');
            Route::delete('tags/{tag_id}', 'delete')->name('delete');
        });
    });

});