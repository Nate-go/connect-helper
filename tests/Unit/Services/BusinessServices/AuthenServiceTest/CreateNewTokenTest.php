<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Constants\AuthenConstant\StatusResponse;
use App\Constants\UserConstant\UserStatus;
use App\Models\Enterprise;
use App\Models\User;
use App\Services\BusinessServices\AuthenService;
use Illuminate\Http\Response;

class CreateNewTokenTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $enterprise = Enterprise::create([
            'name' => 'newEnterprise',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
            'enterprise_id' => $enterprise->id,
        ]);

        $authenServiceMock = $this->getMockService(AuthenService::class, ['response']);
        $this->be($user);
        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => ''], StatusResponse::SUCCESS));

        $response = $authenServiceMock->createNewToken('token', 'rememberToken');
        $this->assertEquals(StatusResponse::SUCCESS, $response->getStatusCode());
    }

    public function testDeactive()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::DEACTIVE,
        ]);

        $authenServiceMock = $this->getMockService(AuthenService::class, ['response']);
        $this->be($user);
        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => ''], StatusResponse::DEACTIVED_ACCOUNT));

        $response = $authenServiceMock->createNewToken('token', 'rememberToken');
        $this->assertEquals(StatusResponse::DEACTIVED_ACCOUNT, $response->getStatusCode());
    }

    public function testBlock()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::DEACTIVE,
        ]);

        $authenServiceMock = $this->getMockService(AuthenService::class, ['response']);
        $this->be($user);
        $authenServiceMock->expects($this->once())
            ->method('response')
            ->willReturn(new Response(['message' => ''], StatusResponse::BLOCKED_ACCOUNT));

        $response = $authenServiceMock->createNewToken('token', 'rememberToken');
        $this->assertEquals(StatusResponse::BLOCKED_ACCOUNT, $response->getStatusCode());
    }
}
