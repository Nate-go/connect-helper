<?php

namespace Tests\Unit\Services\ModelService\ScheduleServiceTest;

use App\Constants\ScheduleConstant\ScheduleStatus;
use App\Constants\ScheduleConstant\ScheduleType;
use App\Constants\UserConstant\UserStatus;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\ScheduleService;
use Tests\Unit\BaseTest;

class CreateScheduleTest extends BaseTest
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

        $schedule = Schedule::create([
            'status' => ScheduleStatus::UNPUBLISH,
            'type' => ScheduleType::ONLINE,
            'place' => '',
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class, ['getCalendarService']);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $this->be($user);
        $gmailTokenService->expects($this->once())
            ->method('getCalendarService')
            ->willReturn($this->getObject(['events' => ['name' => 'name']]));

        $response = $scheduleServiceMock->createSchedule($schedule, []);
        $this->assertTrue($response);
    }
}
