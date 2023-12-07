<?php

namespace Tests\Unit\Services\ModelService\ConnectionHistoryServiceTest;

use App\Models\ConnectionHistory;
use App\Models\Contact;
use App\Models\User;
use App\Services\ModelServices\ConnectionHistoryService;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class GetLastContactedAtTest extends BaseTest
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

        $time = now();

        $connectionHistory = ConnectionHistory::create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'contacted_at' => $time,
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, [], [
            new ConnectionHistory(),
            $this->getMockService(GmailTokenService::class),
        ]);

        $response = $connectionHistoryServiceMock->getLastContactedAt($user, $contact);
        $this->assertEquals($time, $response);
    }

    public function testFail()
    {
        $contact = Contact::create([
            'title' => 'new',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $connectionHistoryServiceMock = $this->getMockService(ConnectionHistoryService::class, [], [
            new ConnectionHistory(),
            $this->getMockService(GmailTokenService::class),
        ]);

        $response = $connectionHistoryServiceMock->getLastContactedAt($user, $contact);
        $this->assertEquals(null, $response);
    }
}
