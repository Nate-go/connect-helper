<?php

namespace Tests\Unit\Services\ModelService\EmailScheduleServiceTest;

use App\Constants\SendMailConstant\SendMailType;
use App\Models\EmailSchedule;
use App\Models\SendMail;
use App\Models\SendMailContact;
use App\Models\User;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\GmailTokenService;
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
        ]);

        $sendMail = SendMail::create([
            'user_id' => $user->id,
            'name' => 'name',
        ]);

        SendMailContact::create([
            'send_mail_id' => $sendMail->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, ['sendMail']);
        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, ['sendMail']);

        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, ['sendMail'], [
            new EmailSchedule(),
            $sendMailContactServiceMock,
            $gmailTokenServiceMock,
        ]);

        $sendMailContactServiceMock
            ->method('sendMail')
            ->willReturn(true);

        $gmailTokenServiceMock
            ->method('sendMail')
            ->willReturn(true);

        $response = $emailScheduleServiceMock->sendMail($sendMail);
        $this->assertEquals(null, $response);
    }

    public function testSendMailPersonal()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
        ]);

        $sendMail = SendMail::create([
            'user_id' => $user->id,
            'type' => SendMailType::PERSONAL,
            'name' => 'name',
        ]);

        SendMailContact::create([
            'send_mail_id' => $sendMail->id,
        ]);

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, ['sendMail']);
        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, ['sendMail']);

        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, ['sendMail'], [
            new EmailSchedule(),
            $sendMailContactServiceMock,
            $gmailTokenServiceMock,
        ]);

        $sendMailContactServiceMock
            ->method('sendMail')
            ->willReturn(true);

        $gmailTokenServiceMock
            ->method('sendMail')
            ->willReturn(true);

        $response = $emailScheduleServiceMock->sendMail($sendMail);
        $this->assertEquals(null, $response);
    }
}
