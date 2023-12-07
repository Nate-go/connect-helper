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

class ShowConnectionTest extends BaseTest
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

        $response = $connectionServiceMock->showConnection($connection->id);
        $this->assertIsObject($response);
    }

    public function testConnectionNotFound()
    {

        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $this->getMockService(ConnectionHistoryService::class),
        ]);

        $response = $connectionServiceMock->showConnection(1);
        $this->assertFalse($response);
    }
}
