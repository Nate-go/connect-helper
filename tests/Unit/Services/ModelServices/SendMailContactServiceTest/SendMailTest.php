<?php

namespace Tests\Unit\Services\ModelService\SendMailContactServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\Contact;
use App\Models\SendMail;
use App\Models\SendMailContact;
use App\Models\User;
use App\Services\ModelServices\SendMailContactService;
use Tests\Unit\BaseTest;

class SendMailTest extends BaseTest
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
            'status' => UserStatus::ACTIVE,
        ]);

        $sendMail = SendMail::create([
            'user_id' => $user->id,
        ]);

        $contact = Contact::create([
            'title' => 'name',
        ]);

        $sendMailContact = SendMailContact::create([
            'send_mail_id' => $sendMail->id,
            'contact_id' => $contact->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, ['addMailToQueue'], [
            new SendMailContact(),
        ]);
        $sendMailContactServiceMock->expects($this->once())
            ->method('addMailToQueue')
            ->willReturn(true);
        $response = $sendMailContactServiceMock->sendMail($sendMailContact->id);
        $this->assertEquals(true, $response);
    }

    public function testNotFound()
    {
        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, [], [
            new SendMailContact(),
        ]);
        $response = $sendMailContactServiceMock->sendMail(1);
        $this->assertEquals(false, $response);
    }
}
