<?php 

namespace App\Services\ModelServices;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\User;

class UserService extends BaseService {
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function getAllOwner() {
        $owners = User::whereNot('role', UserRole::ADMIN)->get();
        return $owners;
    }

    public function isEmailExist($email) {
        return User::where('email', $email)->where('status', UserStatus::ACTIVE)->exists();
    }
}