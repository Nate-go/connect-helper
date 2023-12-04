<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\BaseTest;

class BaseAuthenServiceTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getContructData() {
        return [
            
        ];
    }
}
