<?php 

namespace App\Services\ModelServices;
use App\Constants\UserConstant\UserRole;
use App\Models\User;

class UserService extends BaseService {
    public function getAllOwner() {
        $owners = User::whereNot('role', UserRole::ADMIN)->get();
        return $owners;
    }
}