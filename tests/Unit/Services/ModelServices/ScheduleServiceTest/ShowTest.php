<?php

namespace Tests\Unit\Services\ModelService\ScheduleServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\ScheduleService;
use Tests\Unit\BaseTest;

class ShowTest extends BaseTest
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
            'user_id' => $user->id,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $response = $scheduleServiceMock->show($schedule->id);
        $this->assertIsObject($response);
    }

    public function testNotFound()
    {
        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $response = $scheduleServiceMock->show(1);
        $this->assertFalse($response);
    }
}
