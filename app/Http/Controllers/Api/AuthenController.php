<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessServices\AuthenService;
use Illuminate\Http\Request;

class AuthenController extends Controller
{
    protected $authenService;

    public function __construct(AuthenService $authenService)
    {
        $this->authenService = $authenService; 
    }

    public function login(Request $request)
    {
        return $this->authenService->login($request);
    }

    public function signup(Request $request)
    {
        return $this->authenService->signup($request);
    }

    public function logout()
    {
        return $this->authenService->logout();  
    }

    public function throwAuthenError()
    {
        return $this->authenService->throwAuthenError();
    }

    public function throwAuthorError()
    {
        return $this->authenService->throwAuthorError();
    }

    public function refresh()
    {
        return $this->authenService->refresh();
    }

    public function getUserProfile()
    {
        return $this->authenService->getUserProfile();
    }

    protected function createNewToken($token)
    {
        return $this->authenService->createNewToken($token);
    }

    public function changePassWord(Request $request)
    {
        return $this->authenService->changePassWord($request);
    }

    public function sendVerify(Request $request) {
        return $this->authenService->sendVerify($request->email);
    }

    public function activeAccount(Request $request) {
        return $this->authenService->activeAccount($request);
    }
}
