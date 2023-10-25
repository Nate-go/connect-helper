<?php

namespace App\Constants\AuthenConstant;

use App\Constants\BaseConstant;

class StatusResponse extends BaseConstant
{
    const SUCCESS = 200;

    const NOT_FOUND = 404;

    const ERROR = 500;

    const DEACTIVED_ACCOUNT = 451;

    const BLOCKED_ACCOUNT = 451;

    const UNAUTHORIZED = 401;

    const INVALID_VALIDATE = 422;
}