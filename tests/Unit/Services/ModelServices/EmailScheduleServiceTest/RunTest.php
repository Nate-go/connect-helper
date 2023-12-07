<?php

namespace Tests\Unit\Services\ModelService\EmailScheduleServiceTest;

use App\Constants\EmailScheduleConstant\EmailScheduleStatus;
use App\Models\EmailSchedule;
use App\Models\SendMail;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\SendMailContactService;
use Tests\Unit\BaseTest;

class RunTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $sendMail = SendMail::create([
            'name' => 'name',
        ]);

        EmailSchedule::create([
            'send_mail_id' => $sendMail->id,
            'started_at' => now(),
            'nextTime_at' => now(),
            'after_second' => 100,
            'status' => EmailScheduleStatus::RUNNING,
        ]);

        EmailSchedule::create([
            'send_mail_id' => $sendMail->id,
            'started_at' => now(),
            'nextTime_at' => now(),
            'after_second' => 0,
            'status' => EmailScheduleStatus::RUNNING,
        ]);

        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, ['sendMail'], [
            new EmailSchedule(),
            $this->getMockService(SendMailContactService::class),
            $this->getMockService(GmailTokenService::class),
        ]);

        $emailScheduleServiceMock
            ->method('sendMail')
            ->willReturn(true);

        $response = $emailScheduleServiceMock->run();
        $this->assertTrue($response);
    }
}
