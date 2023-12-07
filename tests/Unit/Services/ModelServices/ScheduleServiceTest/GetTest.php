<?php

namespace Tests\Unit\Services\ModelService\ScheduleServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\ScheduleService;
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
            'status' => UserStatus::ACTIVE,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $response = $scheduleServiceMock->get($user, now(), now()->addHours(1));
        $this->assertIsObject($response);
    }
}
