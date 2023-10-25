<?php

namespace App\Services\BusinessServices;

use App\Constants\AuthenConstant\StatusResponse;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Constants\UserConstant\UserVerifyTime;
use App\Models\AccountVerify;
use App\Models\User;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Validator;

class AuthenService
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), StatusResponse::INVALID_VALIDATE);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], StatusResponse::UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), StatusResponse::INVALID_VALIDATE);
        }

        $data = array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
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

        $this->createVerify($user->email);
        return $user;
    }

    private function generateEncodedString($startTime, $endTime, $userData) {
        $dataToEncode = $startTime . '|' . $endTime . '|' . $userData;
        return md5($dataToEncode);
    }

    private function createVerify($email) {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null; 
        }

        $currentDateTime = new DateTime();
        $overDateTime = clone $currentDateTime;
        $overDateTime->add(new DateInterval(UserVerifyTime::ACTIVE_TIME));

        $verify = AccountVerify::create([
            'user_id' => $user->id,
            'verify_code' => $this->generateEncodedString($currentDateTime->format('Y-m-d H:i:s'), $overDateTime->format('Y-m-d H:i:s'), json_encode($user)),
            'overtimed_at' => $overDateTime->format('Y-m-d H:i:s'),
        ]);

        return $verify->verify_code;
    }

    public function sendVerify($email) {
        $verify_code = $this->createVerify($email);

        if(!$verify_code ) {
            return response()->json([
                'error' => 'Can not find out the email'
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
        return response()->json(["error" => "Unauthenticated", "message" => "You need to login to access"], StatusResponse::UNAUTHORIZED);
    }

    public function throwAuthorError()
    {
        return response()->json(["error" => "Unauthorized", "message" => "You do not have permission to access"], StatusResponse::UNAUTHORIZED);
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
                "error"=> "Your account is deactived"
            ], StatusResponse::DEACTIVED_ACCOUNT);
        }

        if ($user->status == UserStatus::BLOCK) {
            return response()->json([
                "error" => "Your account is blocked"
            ], StatusResponse::BLOCKED_ACCOUNT);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ], StatusResponse::SUCCESS);
    }

    public function activeAccount(Request $request)
    {
        $email = $request->email;
        $verify_code = $request->verify_code;

        $user = User::where('email', $email)
            ->where('status', UserStatus::DEACTIVE)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Can not find user or user is already active'
            ], StatusResponse::ERROR);
        }

        $accountVerify = AccountVerify::where('user_id', $user->id)
            ->where('verify_code', $verify_code)
            ->first();

        if ($accountVerify) {
            if ($accountVerify->overtimed_at > now()) {
                $user->status = UserStatus::ACTIVE;
                $user->save();

                $accountVerify->delete();

                return response()->json([
                    'message' => 'Activate account successfully'
                ], StatusResponse::SUCCESS);
            } else {
                return response()->json([
                    'message' => 'Your verify code is expired'
                ], StatusResponse::ERROR);
            }
        } else {
            return response()->json([
                'message' => 'Your verify code is invalid'
            ], StatusResponse::ERROR);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|confirmed|min:6',
            'verify_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), StatusResponse::INVALID_VALIDATE);
        }

        $verify_code = $request->verify_code;
        $newPassword = $request->new_password;

        $user = auth()->user();

        if (!$user or $user->status == UserStatus::DEACTIVE) {
            return response()->json([
                'message' => 'Can not find user or user is deactive'
            ], StatusResponse::ERROR);
        }

        $accountVerify = AccountVerify::where('user_id', $user->id)
            ->where('verify_code', $verify_code)
            ->first();

        if ($accountVerify) {
            if ($accountVerify->overtimed_at > now()) {
                $user->password = bcrypt($newPassword);
                $user->save();

                $accountVerify->delete();

                return response()->json([
                    'message' => 'Change password successfully'
                ], StatusResponse::SUCCESS);
            } else {
                return response()->json([
                    'message' => 'Your verify code is expired'
                ], StatusResponse::ERROR);
            }
        } else {
            return response()->json([
                'message' => 'Your verify code is invalid'
            ], StatusResponse::ERROR);
        }
    }

}