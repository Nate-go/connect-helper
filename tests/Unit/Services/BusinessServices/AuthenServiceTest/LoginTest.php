<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Constants\AuthenConstant\StatusResponse;
use App\Services\BusinessServices\AuthenService;
use Illuminate\Http\Response;

class LoginTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $input = [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ];
        $authenServiceMock = $this->getMockService(AuthenService::class, ['encryptToken', 'createNewToken', 'authenCreadentials']);

        $authenServiceMock->expects($this->once())
            ->method('encryptToken')
            ->willReturn('rememberToken');

        $authenServiceMock->expects($this->once())
            ->method('authenCreadentials')
            ->willReturn(true);

        $authenServiceMock->expects($this->once())
            ->method('createNewToken')
            ->willReturn(new Response(['message' => 'newToken'], StatusResponse::SUCCESS));

        $response = $authenServiceMock->login($input);
        $this->assertEquals(StatusResponse::SUCCESS, $response->getStatusCode());
    }

    public function testLoginFail()
    {
        $input = [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ];
        $authenServiceMock = $this->getMockService(AuthenService::class, ['authenCreadentials', 'response']);

        $authenServiceMock->expects($this->once())
            ->method('authenCreadentials')
            ->willReturn(false);

        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => 'fail'], StatusResponse::UNAUTHORIZED));

        $response = $authenServiceMock->login($input);
        $this->assertEquals(StatusResponse::UNAUTHORIZED, $response->getStatusCode());
    }
}
