<?php

namespace Tests\Unit\Services\ModelService\UserServiceTest;

use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use Tests\Unit\BaseTest;

class GetCoworkersTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $usereServiceMock = $this->getMockService(UserService::class, ['addMailToQueue'], [
            new User(),
            $gmailTokenService,
        ]);

        $response = $usereServiceMock->getCoworkers($user);
        $this->assertIsObject($response);
    }
}
