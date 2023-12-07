<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Models\Connection;
use App\Models\ConnectionUser;
use App\Models\User;
use App\Services\ModelServices\ConnectionService;
use Tests\Unit\BaseTest;

class DeleteConnectionUserTest extends BaseTest
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

        $connectionUser = ConnectionUser::create([
            'user_id' => $user->id,
            'connection_id' => $connection->id,
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class);

        $response = $connectionServiceMock->deleteConnectionUser($user->id, $connection->id);
        $this->assertEquals(null, $response);
    }
}
