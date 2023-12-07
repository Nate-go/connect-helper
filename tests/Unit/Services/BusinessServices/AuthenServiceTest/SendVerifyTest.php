<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Constants\AuthenConstant\StatusResponse;
use App\Services\BusinessServices\AuthenService;
use Illuminate\Http\Response;

class SendVerifyTest extends BaseAuthenServiceTest
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
            'email' => 'name',
            'password' => 'password',
        ];

        $authenServiceMock = $this->getMockService(AuthenService::class, ['createVerify', 'response']);

        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => 'fail'], StatusResponse::SUCCESS));

        $authenServiceMock->expects($this->once())
            ->method('createVerify')
            ->willReturn(true);

        $response = $authenServiceMock->sendVerify($input);
        $this->assertEquals(StatusResponse::SUCCESS, $response->getStatusCode());
    }

    public function testFail()
    {
        $input = [
            'gmail_token' => ['id_token' => 'data', 'expires_in' => 123],
            'enterprise' => 'new enterprise',
            'email' => 'name',
            'password' => 'password',
        ];

        $authenServiceMock = $this->getMockService(AuthenService::class, ['createVerify', 'response']);

        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => 'fail'], StatusResponse::SUCCESS));

        $authenServiceMock->expects($this->once())
            ->method('createVerify')
            ->willReturn(false);

        $response = $authenServiceMock->sendVerify($input);
        $this->assertEquals(StatusResponse::SUCCESS, $response->getStatusCode());
    }
}
