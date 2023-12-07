<?php

namespace Tests\Unit\Services\ModelService\EmailScheduleServiceTest;

use App\Models\EmailSchedule;
use App\Models\User;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\SendMailContactService;
use Tests\Unit\BaseTest;

class GetTest extends BaseTest
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

        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, [], [
            new EmailSchedule(),
            $this->getMockService(SendMailContactService::class),
            $this->getMockService(GmailTokenService::class),
        ]);
        $this->be($user);
        $response = $emailScheduleServiceMock->get([]);
        $this->assertIsArray($response);
    }
}
