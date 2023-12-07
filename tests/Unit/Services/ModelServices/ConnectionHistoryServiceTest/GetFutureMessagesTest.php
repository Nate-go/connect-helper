<?php

namespace Tests\Unit\Services\ModelService\ConnectionHistoryServiceTest;

use App\Models\Contact;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use Tests\Unit\BaseTest;

class GetFutureMessagesTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $contact = Contact::create([
            'title' => 'new',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $service = $this->getObject([
            'users_messages' => ['name'],
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, ['getLastContactedAt']);
        $connectionHistoryServiceMock->expects($this->once())
            ->method('getLastContactedAt')
            ->willReturn(true);
        $response = $connectionHistoryServiceMock->getFutureMessages($user, $contact, $service);
        $this->assertEquals('messages', $response);
    }

    public function testDoNotHaveLastest()
    {
        $contact = Contact::create([
            'title' => 'new',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $service = $this->getObject([
            'users_messages' => ['name'],
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, ['getLastContactedAt']);
        $connectionHistoryServiceMock->expects($this->once())
            ->method('getLastContactedAt')
            ->willReturn(false);
        $response = $connectionHistoryServiceMock->getFutureMessages($user, $contact, $service);
        $this->assertEquals('messages', $response);
    }
}
