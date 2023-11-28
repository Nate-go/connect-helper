<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenFormRequests\ChangePasswordFormRequest;
use App\Http\Requests\AuthenFormRequests\LoginFormRequest;
use App\Http\Requests\AuthenFormRequests\RefreshFormRequest;
use App\Http\Requests\AuthenFormRequests\SendVerifyFormRequest;
use App\Http\Requests\AuthenFormRequests\SignUpEmployeeFormRequest;
use App\Http\Requests\AuthenFormRequests\SignUpFormRequest;
use App\Http\Requests\AuthenFormRequests\VerifyAccountFormRequest;
use App\Services\BusinessServices\AuthenService;
use App\Services\ModelServices\ConnectionService;
use Http;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenController extends Controller
{
    protected $authenService;
    protected $connectionService;

    public function __construct(AuthenService $authenService, ConnectionService $connectionService)
    {
        $this->authenService = $authenService; 
        $this->connectionService = $connectionService;
    }

    public function login(LoginFormRequest $request)
    {
        return $this->authenService->login($request->all());
    }

    public function signup(SignUpFormRequest $request)
    {
        return $this->authenService->signup($request->all());
    }
    
    public function signupEmployee(SignUpEmployeeFormRequest $request) {
        return $this->authenService->signupEmployee($request->all());
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

    public function refresh(RefreshFormRequest $request)
    {
        $result =  $this->authenService->refresh($request->get('remember_token'));
        if(!$result) {
            return response()->json([
                'error'=> 'Can not find out this remember token',
            ], StatusResponse::ERROR);
        }
        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function getUserProfile()
    {
        return $this->authenService->getUserProfile();
    }

    protected function createNewToken($token)
    {
        return $this->authenService->createNewToken($token, null);
    }

    public function resetPassWord(ChangePasswordFormRequest $request)
    {
        return $this->authenService->resetPassWord($request->all());
    }

    public function sendVerify(SendVerifyFormRequest $request) {
        return $this->authenService->sendVerify($request->all());
    }

    public function activeAccount(VerifyAccountFormRequest $request) {
        return $this->authenService->activeAccount($request->all());
    }

    public function checkInviteToken(Request $request) {
        $result = $this->authenService->checkInviteToken($request->get('token') ?? '');
        if (!$result) {
            return response()->json([
                'message' => 'This invitation is expired or not right',
            ], StatusResponse::ERROR);
        }
        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function test(Request $request) {
        return $this->connectionService->test();
    }
}
