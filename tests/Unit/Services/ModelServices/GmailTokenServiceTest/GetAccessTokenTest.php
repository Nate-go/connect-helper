<?php

namespace Tests\Unit\Services\ModelService\GmailTokenServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\GmailToken;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class GetAccessTokenTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExisted()
    {
        $time = now();

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        GmailToken::create([
            'expired_at' => $time->addHour(),
            'access_token' => 'accessToken',
            'user_id' => $user->id,
        ]);

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, [], [
            new GmailToken(),
        ]);
        $response = $gmailTokenServiceMock->getAccessToken($user);
        $this->assertEquals('accessToken', $response);
    }

    public function testExpired()
    {
        $time = now();

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        GmailToken::create([
            'expired_at' => $time,
            'access_token' => 'accessToken',
            'user_id' => $user->id,
        ]);

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, ['refreshAccessToken'], [
            new GmailToken(),
        ]);
        $gmailTokenServiceMock->expects($this->once())
            ->method('refreshAccessToken')
            ->willReturn(['access_token' => 'accessToken', 'expires_in' => 1000]);

        $response = $gmailTokenServiceMock->getAccessToken($user);
        $this->assertEquals('accessToken', $response);
    }
}
