<?php

namespace App\Services\ModelServices;
use App\Models\Enterprise;

class EnterpriseService extends BaseService
{
    public function create($data) {
        return Enterprise::create($data);
    }

    public function isExisted($name) {
        return Enterprise::where("name", $name)->exists();
    }
}