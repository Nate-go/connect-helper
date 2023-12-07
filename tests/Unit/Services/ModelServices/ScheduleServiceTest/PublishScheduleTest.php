<?php

namespace Tests\Unit\Services\ModelService\ScheduleServiceTest;

use App\Constants\ScheduleConstant\ScheduleStatus;
use App\Constants\UserConstant\UserStatus;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\ScheduleService;
use Tests\Unit\BaseTest;

class PublishScheduleTest extends BaseTest
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
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, ['createSchedule', 'invite'], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $scheduleServiceMock->expects($this->once())
            ->method('createSchedule')
            ->willReturn(true);

        $scheduleServiceMock->expects($this->once())
            ->method('invite')
            ->willReturn(true);

        $this->be($user);
        $response = $scheduleServiceMock->publishSchedule($schedule->id);
        $this->assertTrue($response);
    }

    public function testPublished()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        $schedule = Schedule::create([
            'status' => ScheduleStatus::PUBLISH,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);

        $response = $scheduleServiceMock->publishSchedule($schedule->id);
        $this->assertFalse($response);
    }
}
