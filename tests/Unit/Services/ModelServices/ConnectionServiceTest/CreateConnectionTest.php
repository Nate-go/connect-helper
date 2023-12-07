<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Models\Connection;
use App\Models\Tag;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\ContactService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class CreateConnectionTest extends BaseTest
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

        $tag = Tag::create([
            'name' => 'name',
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class);
        $this->be($user);
        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $connectionHistoryServiceMock,
        ]);

        $response = $connectionServiceMock->createConnection([
            'tagIds' => [$tag->id],
            'data' => [
                'name' => '',
                'note' => '',
                'status' => ConnectionStatus::PUBLIC,
            ],
        ]);

        $this->assertEquals(true, $response);
    }
}
