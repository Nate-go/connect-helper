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

class AddUserConnectionsTest extends BaseTest
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
        $response = $connectionServiceMock->addUserConnections([$connection->id], [$user->id]);
        $this->assertEquals(true, $response);
    }

    public function testConnectionsEmpty()
    {
        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $response = $connectionServiceMock->deleteTagsToConnections([1], []);
        $this->assertEquals(false, $response);
    }
}
