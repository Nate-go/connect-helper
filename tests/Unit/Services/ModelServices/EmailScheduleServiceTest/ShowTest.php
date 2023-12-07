<?php

namespace Tests\Unit\Services\ModelService\EmailScheduleServiceTest;

use App\Models\EmailSchedule;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\SendMailContactService;
use Tests\Unit\BaseTest;

class ShowTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $emailSchedule = EmailSchedule::create([
            'name' => 'name',
        ]);

        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, [], [
            new EmailSchedule(),
            $this->getMockService(SendMailContactService::class),
            $this->getMockService(GmailTokenService::class),
        ]);
        $response = $emailScheduleServiceMock->show($emailSchedule->id);
        $this->assertIsObject($response);
    }

    public function testNotFound()
    {
        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, [], [
            new EmailSchedule(),
            $this->getMockService(SendMailContactService::class),
            $this->getMockService(GmailTokenService::class),
        ]);
        $response = $emailScheduleServiceMock->show(1);
        $this->assertFalse($response);
    }
}
