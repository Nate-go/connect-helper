<?php

namespace Tests\Unit\Services\ModelService\ConnectionHistoryServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ContactConstant\ContactType;
use App\Models\Connection;
use App\Models\ConnectionHistory;
use App\Models\ConnectionUser;
use App\Models\Contact;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class UpdateConnectionHistoriesTest extends BaseTest
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

        $contact = Contact::create([
            'title' => 'new',
            'type' => ContactType::MAIL,
            'connection_id' => $connection->id,
        ]);

        $service = $this->getObject([
            'users_messages' => ['name'],
        ]);

        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, ['getGmailService']);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, [], [
            new ConnectionHistory(),
            $gmailTokenServiceMock,
        ]);

        $gmailTokenServiceMock->expects($this->once())
            ->method('getGmailService')
            ->willReturn(true);
        $response = $connectionHistoryServiceMock->updateConnectionHistories($connection);
        $this->assertEquals(true, $response);
    }

    public function testNotFoundConnection()
    {
        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class);
        $response = $connectionHistoryServiceMock->updateConnectionHistories(0);
        $this->assertEquals(false, $response);
    }
}
