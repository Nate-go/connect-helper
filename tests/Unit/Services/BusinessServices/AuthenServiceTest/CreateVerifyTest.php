<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;

class CreateVerifyTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $input = [
            'gmail_token' => ['id_token' => 'data', 'expires_in' => 123],
            'enterprise' => 'new enterprise',
            'name' => 'name',
            'password' => 'password',
        ];

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class);
        $userServiceMock = $this->getMockService(UserService::class, ['getBy']);
        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class);
        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $authenServiceMock = $this->getMockService(AuthenService::class, ['hash', 'sendMailQueue'], [
            $enterpriseServiceMock,
            $userServiceMock,
            $gmailTokenServiceMock,
            $connectionServiceMock,
        ]);

        $authenServiceMock->expects($this->once())
            ->method('hash')
            ->willReturn('123456');

        $userServiceMock->expects($this->once())
            ->method('getBy')
            ->willReturn($this->getObject(['account_verify' => [
                'verify_code' => '',
                'overtimed_at' => '',
                'deleted_at' => '',
            ]]));

        $authenServiceMock->expects($this->once())
            ->method('sendMailQueue')
            ->willReturn('123456');

        $response = $authenServiceMock->createVerify($input);
        $this->assertIsString($response);
    }

    public function testFail()
    {
        $input = [
            'gmail_token' => ['id_token' => 'data', 'expires_in' => 123],
            'enterprise' => 'new enterprise',
            'name' => 'name',
            'password' => 'password',
        ];

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class);
        $userServiceMock = $this->getMockService(UserService::class, ['getBy']);
        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class);
        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $authenServiceMock = $this->getMockService(AuthenService::class, [], [
            $enterpriseServiceMock,
            $userServiceMock,
            $gmailTokenServiceMock,
            $connectionServiceMock,
        ]);

        $userServiceMock->expects($this->once())
            ->method('getBy')
            ->willReturn(false);

        $response = $authenServiceMock->createVerify($input);
        $this->assertNull($response);
    }
}
