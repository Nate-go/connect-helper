<?php

namespace Tests\Unit\Services\ModelService\SendMailServiceTest;

use App\Constants\SendMailConstant\SendMailType;
use App\Constants\UserConstant\UserStatus;
use App\Models\Contact;
use App\Models\SendMail;
use App\Models\SendMailContact;
use App\Models\User;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\SendMailContactService;
use App\Services\ModelServices\SendMailService;
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
            'type' => SendMailType::PERSONAL,
        ]);

        $contact = Contact::create([
            'title' => 'name',
        ]);

        $sendMailContact = SendMailContact::create([
            'send_mail_id' => $sendMail->id,
            'contact_id' => $contact->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, ['sendMail']);

        $sendMailServiceMock = $this->getMockService(SendMailService::class, [], [
            new SendMail(),
            $sendMailContactServiceMock,
            $this->getMockService(EmailScheduleService::class),
        ]);
        $sendMailContactServiceMock->expects($this->once())
            ->method('sendMail')
            ->willReturn(true);
        $response = $sendMailServiceMock->sendMail($sendMail->id);
        $this->assertEquals(true, $response);
    }

    public function testNotPersonal()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        $sendMail = SendMail::create([
            'user_id' => $user->id,
            'type' => SendMailType::CC,
        ]);

        $contact = Contact::create([
            'title' => 'name',
        ]);

        $sendMailContact = SendMailContact::create([
            'send_mail_id' => $sendMail->id,
            'contact_id' => $contact->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, []);

        $sendMailServiceMock = $this->getMockService(SendMailService::class, [], [
            new SendMail(),
            $sendMailContactServiceMock,
            $this->getMockService(EmailScheduleService::class),
        ]);

        $response = $sendMailServiceMock->sendMail($sendMail->id);
        $this->assertEquals(true, $response);
    }

    public function testNotFound()
    {
        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, []);

        $sendMailServiceMock = $this->getMockService(SendMailService::class, [], [
            new SendMail(),
            $sendMailContactServiceMock,
            $this->getMockService(EmailScheduleService::class),
        ]);
        $response = $sendMailServiceMock->sendMail(1);
        $this->assertEquals(false, $response);
    }
}
