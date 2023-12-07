<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ContactConstant\ContactType;
use App\Models\Connection;
use App\Models\Contact;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\ContactService;
use App\Services\ModelServices\EnterpriseService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class MergeTest extends BaseTest
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

        $connection1 = Connection::create([
            'user_id' => $user->id,
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $connection2 = Connection::create([
            'user_id' => $user->id,
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $contact = Contact::create([
            'title' => 'new',
            'type' => ContactType::MAIL,
            'connection_id' => $connection2->id,
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class, [], [
            new Connection(),
            $this->getMockService(GmailTokenService::class),
            $this->getMockService(ContactService::class),
            $this->getMockService(EnterpriseService::class),
            $this->getMockService(ConnectionHistoryService::class),
        ]);
        $this->be($user);
        $response = $connectionServiceMock->merge([$connection1->id, $connection2->id], $connection1->id);
        $this->assertEquals(true, $response);
    }

    public function testNotMain()
    {
        $connection1 = Connection::create([
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $connection2 = Connection::create([
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
        $response = $connectionServiceMock->merge([$connection1->id, $connection2->id], null);
        $this->assertEquals(false, $response);
    }

    public function testNotUser()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $user2 = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $connection1 = Connection::create([
            'user_id' => $user->id,
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $connection2 = Connection::create([
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
        $this->be($user2);
        $response = $connectionServiceMock->merge([$connection1->id, $connection2->id], $connection1->id);
        $this->assertEquals(false, $response);
    }
}
