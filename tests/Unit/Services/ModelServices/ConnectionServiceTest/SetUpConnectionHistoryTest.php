<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ContactConstant\ContactType;
use App\Models\Connection;
use App\Models\ConnectionUser;
use App\Models\Contact;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\ContactService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class SetUpConnectionHistoryTest extends BaseTest
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

        ConnectionUser::create([
            'user_id' => $user->id,
            'connection_id' => $connection->id,
        ]);

        Contact::create([
            'title' => 'new',
            'type' => ContactType::MAIL,
            'connection_id' => $connection->id,
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, ['setUp']);

        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $connectionHistoryServiceMock,
        ]);

        $connectionHistoryServiceMock->expects($this->once())
            ->method('setUp')
            ->willReturn(true);

        $response = $connectionServiceMock->setUpConnectionHistory($user, 'service');
        $this->assertEquals(true, $response);
    }
}
