<?php

namespace Tests\Unit\Services\ModelService\UserServiceTest;

use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use Tests\Unit\BaseTest;

class IsEmailExistTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $usereServiceMock = $this->getMockService(UserService::class, [], [
            new User(),
            $gmailTokenService,
        ]);

        $response = $usereServiceMock->isEmailExist('email');
        $this->assertIsBool($response);
    }
}
