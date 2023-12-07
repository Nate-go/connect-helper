<?php

namespace Tests\Unit\Services\ModelService\GmailTokenServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\GmailToken;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class SendMailTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExisted()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
            'email' => 'email',
        ]);

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, ['getGmailService'], [
            new GmailToken(),
        ]);

        $gmailTokenServiceMock->expects($this->once())
            ->method('getGmailService')
            ->willReturn($this->getObject(['users_messages' => ['name' => 'name']]));

        $response = $gmailTokenServiceMock->sendMail('type', 'subject', 'content', $user);
        $this->assertEquals(true, $response);
    }
}
