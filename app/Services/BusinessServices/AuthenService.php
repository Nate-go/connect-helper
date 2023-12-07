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
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use DateInterval;
use DateTime;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Hash;

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
        ConnectionService $connectionService,
    ) {
        $this->enterpriseService = $enterpriseService;
        $this->userService = $userService;
        $this->gmailTokenService = $gmailTokenService;
        $this->connectionService = $connectionService;
    }

    public function response($data, $status)
    {
        return response()->json($data, $status);
    }

    public function authenCreadentials($credentials)
    {
        return auth()->attempt($credentials);
    }

    public function hash($data)
    {
        return Hash::make($data);
    }

    public function setUpUser($user)
    {
        SetupDataForUser::dispatch($user);
    }

    public function sendMailQueue($user)
    {
        SendMailQueue::dispatch($user);
    }

    public function login($input)
    {
        $remember = $input['remember'] ?? false;
        $rememberToken = null;
        $credentials = ['email' => $input['email'], 'password' => $input['password']];

        if (! $token = $this->authenCreadentials($credentials)) {
            return $this->response(['message' => 'User authentication failed'], StatusResponse::UNAUTHORIZED);
        }

        if ($remember) {
            $rememberToken = $this->encryptToken(array_merge(
                $credentials,
                [
                    'remember' => true,
                    'time' => now(),
                ]
            ));
        }

        return $this->createNewToken($token, $rememberToken);
    }

    public function encryptToken($data)
    {
        $key = Key::loadFromAsciiSafeString(EncryptionKey::REFRESH_KEY);
        $encryptedData = Crypto::encrypt(json_encode($data), $key);

        return $encryptedData;
    }

    public function decryptToken($encryptedData)
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
            return $this->response([
                'message' => 'This email has been used',
            ], StatusResponse::ERROR);
        }

        if ($this->enterpriseService->isExisted($input['enterprise'])) {
            return $this->response([
                'message' => 'This enterprise has been used',
            ], StatusResponse::ERROR);
        }

        $enterprise = $this->enterpriseService->create([
            'name' => $input['enterprise'],
        ]);

        $user = $this->userService->create([
            'enterprise_id' => $enterprise->id,
            'email' => $data['email'],
            'name' => $input['name'],
            'password' => $this->hash($input['password']),
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
            'image_url' => $data['picture'],
        ]);

        $this->gmailTokenService->create(array_merge(
            $gmailToken,
            [
                'user_id' => $user->id,
                'expired_at' => now()->addSeconds($gmailToken['expires_in']),
            ]
        ));

        $this->setUpUser($user);

        return $this->response([
            'message' => $user ? 'User successfully registered' : 'User fail registered',
            'user' => $user,
        ], $user ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function signupEmployee($input)
    {
        $enterpriseId = $this->getEnterpriseId($input['token']);

        if (! $enterpriseId) {
            return $this->response([
                'message' => 'Token is not true',
            ], StatusResponse::ERROR);
        }

        $gmailToken = $input['gmail_token'];
        $data = $this->gmailTokenService->getEmailInforFromToken($gmailToken['id_token']);

        if ($this->userService->isEmailExist($data['email'])) {
            return $this->response([
                'message' => 'This email has been used',
            ], StatusResponse::ERROR);
        }

        $user = $this->userService->create([
            'enterprise_id' => $enterpriseId,
            'email' => $data['email'],
            'name' => $input['name'],
            'password' => $this->hash($input['password']),
            'role' => UserRole::EMPLOYEE,
            'status' => UserStatus::ACTIVE,
            'image_url' => $data['picture'],
        ]);

        $this->gmailTokenService->create(
            array_merge(
                $gmailToken,
                [
                    'user_id' => $user->id,
                    'expiresed_at' => now()->addSeconds($gmailToken['expires_in']),
                ]
            )
        );

        $this->setUpUser($user);

        return $this->response([
            'message' => $user ? 'User successfully registered' : 'User fail registered',
            'user' => $user,
        ], $user ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function getEnterpriseId($token)
    {
        $data = $this->decryptToken($token);
        if (! isset($data['enterpriseId'])) {
            return false;
        }

        return $data['enterpriseId'];
    }

    public function generateEncodedString($startTime, $endTime, $userData)
    {
        $dataToEncode = $startTime.'|'.$endTime.'|'.$userData;

        return $this->hash($dataToEncode);
    }

    public function createVerify($email)
    {
        $user = $this->userService->getBy('email', $email);

        if (! $user) {
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

        $this->sendMailQueue($user);

        return $verify->verify_code;
    }

    public function sendVerify($input)
    {
        $verify_code = $this->createVerify($input['email']);

        if (! $verify_code) {
            return $this->response([
                'message' => 'Can not find out the email',
            ], StatusResponse::ERROR);
        }

        return $this->response([
            'message' => 'Send verify code successfully',
        ], StatusResponse::SUCCESS);
    }

    public function throwAuthenError()
    {
        return $this->response(['message' => 'You need to login to access'], StatusResponse::UNAUTHORIZED);
    }

    public function throwAuthorError()
    {
        return $this->response(['message' => 'You do not have permission to access'], StatusResponse::UNAUTHORIZED);
    }

    public function refresh($rememberToken)
    {
        $user = $this->userService->getBy('remember_token', $rememberToken);

        if (! $user) {
            return false;
        }

        $data = $this->decryptToken($rememberToken);

        return $this->login($data);
    }

    public function getUserProfile()
    {
        return $this->response(auth()->user(), StatusResponse::SUCCESS);
    }

    public function createNewToken($token, $rememberToken)
    {
        $user = auth()->user();

        if ($user->status == UserStatus::DEACTIVE) {
            return $this->response([
                'message' => 'Your account is deactived',
            ], StatusResponse::DEACTIVED_ACCOUNT);
        }

        if ($user->status == UserStatus::BLOCK) {
            return $this->response([
                'message' => 'Your account is blocked',
            ], StatusResponse::BLOCKED_ACCOUNT);
        }

        $user->remember_token = $rememberToken;
        $user->save();

        return $this->response([
            'access_token' => $token,
            'remember_token' => $rememberToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => json_decode(json_encode(new UserInformation(auth()->user()))),
        ], StatusResponse::SUCCESS);
    }

    public function checkInviteToken($token)
    {
        $data = $this->decryptToken($token);
        if (! isset($data['enterprise']) && ! ($data['expired_at'] > now())) {
            return false;
        }

        return [
            'enterprise' => $data['enterprise'],
            'token' => $this->encryptToken([
                'enterpriseId' => $data['enterprise']['id'],
                'time' => now(),
            ]),
        ];
    }
}
