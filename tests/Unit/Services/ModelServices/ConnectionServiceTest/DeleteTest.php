<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Models\Connection;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\ContactService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class DeleteTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $connection = Connection::create([
            'user_id' => $user->id,
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $this->getMockService(ConnectionHistoryService::class),
        ]);
        $this->be($user);
        $response = $connectionServiceMock->delete([$connection->id]);
        $this->assertEquals(1, $response);
    }

    public function testNotOwner()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $connection = Connection::create([
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $this->getMockService(ConnectionHistoryService::class),
        ]);
        $this->be($user);
        $response = $connectionServiceMock->delete([$connection->id]);
        $this->assertEquals(false, $response);
    }
}
