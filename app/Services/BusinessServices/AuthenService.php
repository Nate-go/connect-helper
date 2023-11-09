<?php

namespace App\Services\BusinessServices;

use App\Constants\AuthenConstant\StatusResponse;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Constants\UserConstant\UserVerifyTime;
use App\Http\Resources\UserInformation;
use App\Jobs\SendMailQueue;
use App\Jobs\SetupDataForUser;

use App\Models\User;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use DateInterval;
use DateTime;
use Hash;
use Illuminate\Http\Request;


class AuthenService
{
    protected $enterpriseService;

    protected $userService;

    protected $gmailTokenService;

    protected $connectionService;

    public function __construct(
            EnterpriseService $enterpriseService, 
            UserService $userService, 
            GmailTokenService $gmailTokenService,
            ConnectionService $connectionService
        ) {
        $this->enterpriseService = $enterpriseService;
        $this->userService = $userService;
        $this->gmailTokenService = $gmailTokenService;
        $this->connectionService = $connectionService;
    }

    public function login($input) {
       
        if (!$token = auth()->attempt($input)) {
            return response()->json(["message" => 'Unauthorized'], StatusResponse::UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    public function signup($input)
    {
        $gmailToken = $input['gmail_token'];
        $email = $this->gmailTokenService->getEmailFromToken($gmailToken['access_token']);

        if ($this->userService->isEmailExist($email)) {
            return response()->json([
                'message' => 'This email has been used',
            ], StatusResponse::ERROR);
        }

        if ($this->enterpriseService->isExisted($input['enterprise'])) {
            return response()->json([
                'message' => 'This enterprise has been used',
            ], StatusResponse::ERROR);
        }

        $enterprise = $this->enterpriseService->create([
            'name' => $input['enterprise']
        ]);

        $user = $this->userService->create([
            'enterprise_id' => $enterprise->id,
            'email' => $email,
            'name' => $input['name'],
            'password' => Hash::make($input['password']),
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->gmailTokenService->create(array_merge(
                $gmailToken, 
                ['user_id' => $user->id]
            )
        );

        SetupDataForUser::dispatch($user);

        return response()->json([
            'message' => $user ? 'User successfully registered' : 'User fail registered',
            'user' => $user
        ], $user ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    private function generateEncodedString($startTime, $endTime, $userData) {
        $dataToEncode = $startTime . '|' . $endTime . '|' . $userData;
        return Hash::make($dataToEncode);
    }

    private function createVerify($email) {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null; 
        }

        $currentDateTime = new DateTime();
        $overDateTime = clone $currentDateTime;
        $overDateTime->add(new DateInterval(UserVerifyTime::ACTIVE_TIME));

        $verify = $user->account_verify;
        $verify->verify_code = $this->generateEncodedString($currentDateTime->format('Y-m-d H:i:s'), $overDateTime->format('Y-m-d H:i:s'), json_encode($user));
        $verify->overtimed_at = $overDateTime->format('Y-m-d H:i:s');
        $verify->deleted_at = null;
        $verify->save();

        SendMailQueue::dispatch($user);

        return $verify->verify_code;
    }

    public function sendVerify($input) {
        $verify_code = $this->createVerify($input['email']);

        if(!$verify_code ) {
            return response()->json([
                "message" => 'Can not find out the email'
            ], StatusResponse::ERROR);
        }

        return response()->json([
            'message'=> 'Send verify code successfully',
        ], StatusResponse::SUCCESS);
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out'], StatusResponse::SUCCESS);
    }

    public function throwAuthenError()
    {
        return response()->json(["message" => "You need to login to access"], StatusResponse::UNAUTHORIZED);
    }

    public function throwAuthorError()
    {
        return response()->json(["message" => "You do not have permission to access"], StatusResponse::UNAUTHORIZED);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function getUserProfile()
    {
        return response()->json(auth()->user(), StatusResponse::SUCCESS);
    }

    protected function createNewToken($token)
    {
        $user = auth()->user();

        if($user->status == UserStatus::DEACTIVE) {
            return response()->json([
                "message"=> "Your account is deactived"
            ], StatusResponse::DEACTIVED_ACCOUNT);
        }

        if ($user->status == UserStatus::BLOCK) {
            return response()->json([
                "message" => "Your account is blocked"
            ], StatusResponse::BLOCKED_ACCOUNT);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => json_decode(json_encode(new UserInformation(auth()->user())))
        ], StatusResponse::SUCCESS);
    }

    public function activeAccount($input)
    {
        $email = $input['email'];
        $verify_code = $input['verify_code'];

        $user = User::where('email', $email)
            ->where('status', UserStatus::DEACTIVE)
            ->first();

        $result = $this->checkVerifyAccount($user, $verify_code);
        
        if ($result) {
            return $result;
        }

        $user->status = UserStatus::ACTIVE;
        $user->account_verify->delete();
        $user->save();

        return response()->json([
            'message' => 'Activate account successfully'
        ], StatusResponse::SUCCESS);
    }

    public function resetPassword($input)
    {
        $verify_code = $input['verify_code'];
        $newPassword = $input['new_password'];

        $user = auth()->user();

        $result = $this->checkVerifyAccount($user, $verify_code);

        if ($result) {
            return $result;
        }

        $user->password = bcrypt($newPassword);
        $user->account_verify->delete();
        $user->save();

        return response()->json([
            'message' => 'Change password successfully'
        ], StatusResponse::SUCCESS);
    }

    private function checkVerifyAccount($user, $verify_code)
    {
        if (!$user) {
            return response()->json([
                'message' => 'Can not find user or user is already active'
            ], StatusResponse::ERROR);
        }

        $accountVerify = $user->account_verify;

        if (!$accountVerify or $accountVerify->overtimed_at < now() or $accountVerify->verify_code != $verify_code) {
            return response()->json([
                'message' => 'Your verify code is invalid'
            ], StatusResponse::ERROR);
        }

        return null;
    }

    public function redirectToGoogle()
    {
        $service = $this->gmailTokenService->getGmailService(2);

        $this->connectionService->setUp($service);
    }

    // public function redirectToGoogle()
    // {
    //     $client = new Google_Client();
    //     $client->setClientId(env('GOOGLE_CLIENT_ID'));
    //     $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
    //     $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
    //     $client->addScope('email');
    //     $client->addScope('profile');
    //     $client->setAccessType('offline'); // Add this line

    //     dd($client->createAuthUrl());
    //     return redirect($client->createAuthUrl());
    // }

    // public function handleGoogleCallback(Request $request)
    // {
    //     $client = new Google_Client();
    //     $client->setClientId(env('GOOGLE_CLIENT_ID'));
    //     $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
    //     $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
    //     $client->addScope('email');
    //     $client->addScope('profile');

    //     $client->authenticate($request->get('code'));
    //     $accessToken = $client->getAccessToken();

    //     dd($accessToken);

    //     // $accessToken contains the access token and refresh token

    //     return response()->json($accessToken);
    // }
}