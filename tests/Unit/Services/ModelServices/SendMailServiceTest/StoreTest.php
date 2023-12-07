<?php

namespace Tests\Unit\Services\ModelService\SendMailServiceTest;

use App\Constants\SendMailConstant\SendMailType;
use App\Constants\UserConstant\UserStatus;
use App\Models\SendMail;
use App\Models\User;
use App\Services\ModelServices\EmailScheduleService;
use App\Services\ModelServices\SendMailContactService;
use App\Services\ModelServices\SendMailService;
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

        $data = [
            'name' => 'name',
            'title' => 'title',
            'content' => 'content',
            'type' => SendMailType::BCC,
            'contactIds' => [],
        ];

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, ['sendMail']);

        $sendMailServiceMock = $this->getMockService(SendMailService::class, ['sendMail'], [
            new SendMail(),
            $sendMailContactServiceMock,
            $this->getMockService(EmailScheduleService::class),
        ]);

        $sendMailServiceMock->expects($this->once())
            ->method('sendMail')
            ->willReturn(true);
        $this->be($user);
        $response = $sendMailServiceMock->store($data);
        $this->assertEquals(true, $response);
    }

    public function testHasSchedule()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        $data = [
            'name' => 'name',
            'title' => 'title',
            'content' => 'content',
            'type' => SendMailType::BCC,
            'schedule' => [
                'started_at' => now(),
                'after_second' => 1000,
            ],
            'contactIds' => [],
        ];

        $sendMailContactServiceMock = $this->getMockService(SendMailContactService::class, []);
        $emailScheduleServiceMock = $this->getMockService(EmailScheduleService::class, ['create']);
        $sendMailServiceMock = $this->getMockService(SendMailService::class, [], [
            new SendMail(),
            $sendMailContactServiceMock,
            $emailScheduleServiceMock,
        ]);

        $emailScheduleServiceMock->expects($this->once())
            ->method('create')
            ->willReturn(true);

        $this->be($user);
        $response = $sendMailServiceMock->store($data);
        $this->assertEquals(true, $response);
    }
}
