<?php

namespace App\Services\ModelServices;
use App\Models\Enterprise;
use App\Models\User;

class EnterpriseService extends BaseService
{
    public function __construct(Enterprise $enterprise) {
        $this->model = $enterprise;
    }

    public function isExisted($name) {
        return Enterprise::where("name", $name)->exists();
    }

    public function getUserEnterprise($userId) {
        $user = User::where("user_id", $userId)->first();
        if (!$user) {
            return null;
        }

        return $user->enterprise;
    }
}