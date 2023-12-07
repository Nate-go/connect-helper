<?php

namespace Tests\Unit\Services\ModelService\ScheduleServiceTest;

use App\Constants\ScheduleConstant\ScheduleStatus;
use App\Constants\UserConstant\UserStatus;
use App\Models\Contact;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ModelServices\GmailTokenService;
use App\Services\ModelServices\ScheduleService;
use Tests\Unit\BaseTest;

class DeleteMembersTest extends BaseTest
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

        $contact = Contact::create([
            'title' => 'name',
        ]);

        $schedule = Schedule::create([
            'status' => ScheduleStatus::UNPUBLISH,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);

        $response = $scheduleServiceMock->deleteMembers($schedule->id, [$user->id], [$contact->id]);
        $this->assertTrue($response);
    }

    public function testPublished()
    {
        $schedule = Schedule::create([
            'status' => ScheduleStatus::PUBLISH,
        ]);

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, [], [
            new Schedule(),
            $gmailTokenService,
        ]);

        $response = $scheduleServiceMock->deleteMembers($schedule->id, [], []);
        $this->assertFalse($response);
    }
}
