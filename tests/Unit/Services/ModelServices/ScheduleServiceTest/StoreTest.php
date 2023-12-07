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

class StoreTest extends BaseTest
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

        $data = [
            'user_id' => $user->id,
            'userIds' => [$user->id],
            'contactIds' => [$contact->id],
            'status' => ScheduleStatus::PUBLISH,
        ];

        $gmailTokenService = $this->getMockService(GmailTokenService::class);

        $scheduleServiceMock = $this->getMockService(ScheduleService::class, ['publishSchedule'], [
            new Schedule(),
            $gmailTokenService,
        ]);
        $scheduleServiceMock->expects($this->once())
            ->method('publishSchedule')
            ->willReturn(true);
        $response = $scheduleServiceMock->store($data);
        $this->assertTrue($response);
    }
}
