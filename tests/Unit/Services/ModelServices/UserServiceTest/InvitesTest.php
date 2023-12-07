<?php

namespace Tests\Unit\Services\ModelService\UserServiceTest;

use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\Enterprise;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use Tests\Unit\BaseTest;

class InvitesTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $enterprise = Enterprise::create([
            'name' => 'name',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
            'enterprise_id' => $enterprise->id,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $usereServiceMock = $this->getMockService(UserService::class, ['addMailToQueue'], [
            new User(),
            $gmailTokenService,
        ]);

        $usereServiceMock->expects($this->once())
            ->method('addMailToQueue')
            ->willReturn(true);
        $this->be($user);
        $response = $usereServiceMock->invites([]);
        $this->assertTrue($response);
    }
}
