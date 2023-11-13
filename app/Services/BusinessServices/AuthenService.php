<?php

namespace App\Services\BusinessServices;

use App\Constants\AuthenConstant\EncryptionKey;
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
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Google_Client;
use Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;


class AuthenService
{
    protected $enterpriseService;

    protected $userService;

    protected $gmailTokenService;

    protected $connectionService;

    protected $tokenRespone = [];

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
        $remember = $input['remember'] ?? false;
        $rememberToken = null;
        $credentials = ['email'=> $input['email'],'password'=> $input['password']];

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(["message" => 'Unauthorized'], StatusResponse::UNAUTHORIZED);
        }

        if ($remember) {
            $rememberToken = $this->encryptToken(array_merge(
                $credentials, 
                [
                    'remember' => true,
                    'time' => now()
                ]
            ));
        }

        return $this->createNewToken($token, $rememberToken);
    }

    private function encryptToken($data)
    {
        $key = Key::loadFromAsciiSafeString(EncryptionKey::REFRESH_KEY);
        $encryptedData = Crypto::encrypt(json_encode($data), $key);
        return $encryptedData;
    }

    private function decryptToken($encryptedData)
    {
        $key = Key::loadFromAsciiSafeString(EncryptionKey::REFRESH_KEY);
        $decryptedData = Crypto::decrypt($encryptedData, $key);
        return json_decode($decryptedData, true);
    }

    public function signup($input)
    {
        $gmailToken = $input['gmail_token'];
        $data = $this->gmailTokenService->getEmailInforFromToken($gmailToken['id_token']);

        if ($this->userService->isEmailExist($data['email'])) {
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
            'email' => $data['email'],
            'name' => $input['name'],
            'password' => Hash::make($input['password']),
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
            'image_url' => $data['picture']
        ]);

        $this->gmailTokenService->create(array_merge(
                $gmailToken, 
                [
                    'user_id' => $user->id,
                    'expiresed_at' => now()->addSeconds($gmailToken['expires_in'])
                ]
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

    public function refresh($rememberToken)
    {
        $user = User::where("remember_token", $rememberToken)->first();

        if(!$user) {
            return false;
        }

        $data = $this->decryptToken($rememberToken);
        return $this->login($data);
    }

    public function getUserProfile()
    {
        return response()->json(auth()->user(), StatusResponse::SUCCESS);
    }

    protected function createNewToken($token, $rememberToken)
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

        $user->remember_token = $rememberToken;
        $user->save();

        return response()->json([
            'access_token' => $token,
            'remember_token' => $rememberToken,
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
}