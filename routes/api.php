<?php

use App\Constants\UserConstant\UserRole;
use App\Http\Controllers\Api\AuthenController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\ConnectionHistoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SendMailController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\TemplateGroupController;
use App\Http\Controllers\Api\UserController;
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
Route::post('/refresh', [AuthenController::class, 'refresh'])->name('refresh');
Route::post('/test', [AuthenController::class, 'test'])->name('test');
Route::post('/check-invite-token', [AuthenController::class, 'checkInviteToken'])->name('checkInviteToken');
Route::post('/signup-employee', [AuthenController::class, 'signupEmployee'])->name('auth.signupEmployee');

Route::group(['prefix' => 'auth'], function () {
    Route::get('/google', [AuthenController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthenController::class, 'handleGoogleCallback']);
    Route::get('/google/token', [AuthenController::class, 'getGoogleToken']);
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
            Route::post('/reset-pass', 'resetPassWord')->name('resetPassword');
            Route::get('/user-profile', 'getUserProfile')->name('getUserProfile');
        });
    });

    Route::controller(ConnectionHistoryController::class)->group(function () {
        Route::prefix('connection-histories')->name('connectionHistory.')->group(function () {
            Route::put('/{connectionId}', 'updateConnection')->name('updateConnection');
            Route::put('', 'updateUserConnection')->name('updateUserConnection');
        });
    });

    Route::controller(ConnectionController::class)->group(function () {
        Route::prefix('connections')->name('connection.')->group(function () {
            Route::get('', 'index')->name('getConnections');
            Route::delete('', 'destroy')->name('deleteConnections');
            Route::get('/merge', 'merge')->name('megreConnections');
            Route::get('/{connectionId}', 'show')->name('showConnection');
            Route::put('', 'update')->name('updateConnections');
            Route::post('/addTags', 'addTags')->name('addTagToConnections');
            Route::post('/deleteTags', 'deleteTags')->name('deleteTagToConnections');
            Route::post('', 'store')->name('createConnection');
            Route::put('/{connectionId}', 'edit')->name('editConnection');
            Route::get('/{connectionId}/contacts', 'getContacts')->name('getContacts');
            Route::post('/add-user-connections', 'addUserConnections')->name('addUserConnection');
            Route::post('/delete-user-connections', 'deleteUserConnections')->name('deleteUserConnection');
            Route::get('/user-connections/useable', 'getUserConnections')->name('getUserConnection');
        });
    });

    Route::controller(TagController::class)->group(function () {
        Route::prefix('tags')->name('tag.')->group(function () {
            Route::get('', 'index')->name('get');
            Route::post('', 'store')->name('store');
            Route::get('/{tag_id}', 'edit')->name('detail');
            Route::put('/{tag_id}', 'update')->name('update');
            Route::delete('', 'destroy')->name('delete');
        });
    });

    Route::controller(ContactController::class)->group(function () {
        Route::prefix('contacts')->name('contact.')->group(function () {
            Route::put('/{tag_id}', 'update')->name('update');
            Route::delete('/{tag_id}', 'destroy')->name('delete'); 
            Route::post('', 'store')->name('store');
        });
    });

    Route::controller(UserController::class)->group(function () {
        Route::prefix('users')->name('user.')->group(function () {
            Route::get('/coworkers', 'getCoworkers')->name('getCoworkers');
            Route::post('/invites', 'invites')->name('invites');
            Route::get('/accept-invite', 'acceptInvite')->name('acceptInvites');
        });
    });

    Route::controller(SendMailController::class)->group(function () {
        Route::prefix('send-mails')->name('sendMail.')->group(function () {
            Route::post('', 'store')->name('store');
        });
    });

    Route::controller(TemplateController::class)->group(function () {
        Route::prefix('templates')->name('template.')->group(function () {
            Route::post('', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('/{id}', 'delete')->name('delete');
            Route::put('/{id}', 'update')->name('update');
            Route::get('', 'index')->name('index');
        });
    });

    Route::controller(TemplateGroupController::class)->group(function () {
        Route::prefix('template-groups')->name('templateGroup.')->group(function () {
            Route::post('', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('', 'delete')->name('delete');
            Route::get('', 'index')->name('index');
            Route::put('', 'update')->name('update');
        });
    });

    Route::controller(ScheduleController::class)->group(function () {
        Route::prefix('schedules')->name('schedule.')->group(function () {
            Route::post('', 'store')->name('store');
            Route::post('/{id}/add-members', 'addMembers')->name('addMember');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('', 'delete')->name('delete');
            Route::delete('/{id}/delete-members', 'deleteMembers')->name('delete');
            Route::get('', 'index')->name('index');
            Route::put('', 'update')->name('update');
            Route::put('/{id}/publish', 'publish')->name('publish');
        });
    });

});