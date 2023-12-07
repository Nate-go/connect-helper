<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;

class RefreshTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class);
        $userServiceMock = $this->getMockService(UserService::class, ['getBy']);
        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class);
        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $authenServiceMock = $this->getMockService(AuthenService::class, ['decryptToken', 'login'], [
            $enterpriseServiceMock,
            $userServiceMock,
            $gmailTokenServiceMock,
            $connectionServiceMock,
        ]);

        $authenServiceMock->expects($this->once())
            ->method('decryptToken')
            ->willReturn('data');

        $authenServiceMock->expects($this->once())
            ->method('login')
            ->willReturn('true');

        $userServiceMock->expects($this->once())
            ->method('getBy')
            ->willReturn(true);

        $response = $authenServiceMock->refresh('token');
        $this->assertEquals(true, $response);
    }

    public function testNotFindOutUser()
    {
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

        $response = $authenServiceMock->refresh('token');
        $this->assertEquals(false, $response);
    }
}
