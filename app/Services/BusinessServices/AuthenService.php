<?php

namespace App\Services\BusinessServices;

use App\Constants\AuthenConstant\StatusResponse;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Constants\UserConstant\UserVerifyTime;
use App\Http\Requests\LoginFormRequest;
use App\Models\AccountVerify;
use App\Models\User;
use DateInterval;
use DateTime;
use Hash;
use Illuminate\Http\Request;
use Validator;

class AuthenService
{
    public function login($input) {
       
        if (!$token = auth()->attempt($input)) {
<<<<<<< HEAD
            return response()->json(["message" => 'Unauthorized'], StatusResponse::UNAUTHORIZED);
=======
            return response()->json(['error' => 'Unauthorized'], StatusResponse::UNAUTHORIZED);
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
        }

        return $this->createNewToken($token);
    }

    public function signup($input)
    {
        $data = array_merge(
            $input,
            ['password' => Hash::make($input['password'])],
            ['role' => UserRole::OWNER],
            ['status' => UserStatus::DEACTIVE]
        );

        $user = $this->setUpUser($data);

        return response()->json([
            'message' => $user ? 'User successfully registered' : 'User fail registered',
            'user' => $user
        ], $user ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    private function setUpUser($data) {
        $user = User::create(
            $data
        );

        AccountVerify::create([
            'user_id'=> $user->id
        ]);

        $this->createVerify($user->email);
        return $user;
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

        $verify = AccountVerify::withTrashed()->where('user_id', $user->id)->first();
        $verify->verify_code = $this->generateEncodedString($currentDateTime->format('Y-m-d H:i:s'), $overDateTime->format('Y-m-d H:i:s'), json_encode($user));
        $verify->overtimed_at = $overDateTime->format('Y-m-d H:i:s');
        $verify->deleted_at = null;
        $verify->save();

        return $verify->verify_code;
    }

    public function sendVerify($input) {
        $verify_code = $this->createVerify($input['email']);

        if(!$verify_code ) {
            return response()->json([
<<<<<<< HEAD
                "message" => 'Can not find out the email'
=======
                'error' => 'Can not find out the email'
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
            ], StatusResponse::ERROR);
        }

        return response()->json([
            'message'=> 'Send verify code successfully',
            'verify_code' => $verify_code,
        ], StatusResponse::SUCCESS);
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out'], StatusResponse::SUCCESS);
    }

    public function throwAuthenError()
    {
<<<<<<< HEAD
        return response()->json(["message" => "You need to login to access"], StatusResponse::UNAUTHORIZED);
=======
        return response()->json(["error" => "Unauthenticated", "message" => "You need to login to access"], StatusResponse::UNAUTHORIZED);
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
    }

    public function throwAuthorError()
    {
<<<<<<< HEAD
        return response()->json(["message" => "You do not have permission to access"], StatusResponse::UNAUTHORIZED);
=======
        return response()->json(["error" => "Unauthorized", "message" => "You do not have permission to access"], StatusResponse::UNAUTHORIZED);
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
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
<<<<<<< HEAD
                "message"=> "Your account is deactived"
=======
                "error"=> "Your account is deactived"
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
            ], StatusResponse::DEACTIVED_ACCOUNT);
        }

        if ($user->status == UserStatus::BLOCK) {
            return response()->json([
<<<<<<< HEAD
                "message" => "Your account is blocked"
=======
                "error" => "Your account is blocked"
>>>>>>> fb1ca02774e5a74b4ca48ccc6201f2bc339839db
            ], StatusResponse::BLOCKED_ACCOUNT);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
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