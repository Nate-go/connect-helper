<?php

namespace Tests\Unit\Services\ModelService\SendMailContactServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\UserConstant\UserStatus;
use App\Models\Connection;
use App\Models\Contact;
use App\Models\Enterprise;
use App\Models\SendMailContact;
use App\Models\User;
use App\Services\ModelServices\SendMailContactService;
use Tests\Unit\BaseTest;

class ReplaceDataTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $enterprise = Enterprise::create([
            'name' => 'new',
        ]);

        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
            'enterprise_id' => $enterprise->id,
        ]);

        $connection = Connection::create([
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $contact = Contact::create([
            'title' => 'name',
            'connection_id' => $connection->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, [], [
            new SendMailContact(),
        ]);
        $this->be($user);
        $response = $sendMailContactServiceMock->replaceData('content', $contact->id);
        $this->assertEquals('content', $response);
    }
}
