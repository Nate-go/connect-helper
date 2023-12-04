<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;
use App\Constants\AuthenConstant\StatusResponse;

use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\UserService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\BusinessServices\AuthenService;
use Illuminate\Http\Response;
use Mockery;

class LoginTest extends BaseAuthenServiceTest
{
    use RefreshDatabase;

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
            ->willReturn("rememberToken");

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
