<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;

class CheckInviteTokenTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $authenServiceMock = $this->getMockService(AuthenService::class, ['decryptToken', 'encryptToken']);
        $authenServiceMock->expects($this->once())
            ->method('decryptToken')
            ->willReturn([
                'enterprise' => ['id' => ''],
                'expired_at' => now(),
            ]);

        $authenServiceMock->expects($this->once())
            ->method('encryptToken')
            ->willReturn('');
        $response = $authenServiceMock->checkInviteToken('testData');
        $this->assertIsArray($response);
    }

    public function testFail()
    {
        $authenServiceMock = $this->getMockService(AuthenService::class, ['decryptToken']);
        $authenServiceMock->expects($this->once())
            ->method('decryptToken')
            ->willReturn([
                'enterprise_other' => ['id' => ''],
                'expired_at' => now(),
            ]);
        $response = $authenServiceMock->checkInviteToken('testData');
        $this->assertFalse($response);
    }
}
