<?php 

namespace App\Services\ModelServices;
use App\Constants\UserConstant\UserRole;
use App\Models\User;

class UserService extends BaseService {
    public function getAllOwner() {
        return $this->midleware([
            $this->author([UserRole::ADMIN])
        ], function () {
            $owners = User::where('role', UserRole::OWNER)->get();
            return $owners;
        });
    }
}