<?php

namespace Tests\Unit\Services\ModelService\TemplateGroupServiceTest;

use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\TemplateGroup;
use App\Models\User;
use App\Services\ModelServices\TemplateGroupService;
use App\Services\ModelServices\TemplateService;
use Tests\Unit\BaseTest;

class SetUpTest extends BaseTest
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
            'role' => UserRole::OWNER,
            'status' => UserStatus::ACTIVE,
        ]);

        $templateServiceMock = $this->getMockService(TemplateService::class, ['create']);

        $templateGroupServiceMock = $this->getMockService(TemplateGroupService::class, [], [
            new TemplateGroup(),
            $templateServiceMock,
        ]);

        $templateServiceMock
            ->method('create')
            ->willReturn(true);

        $response = $templateGroupServiceMock->setUp($user);
        $this->assertEquals(true, $response);
    }

    public function testNotOwner()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => UserRole::EMPLOYEE,
            'status' => UserStatus::ACTIVE,
        ]);

        $templateServiceMock = $this->getMockService(TemplateService::class, []);

        $templateGroupServiceMock = $this->getMockService(TemplateGroupService::class, [], [
            new TemplateGroup(),
            $templateServiceMock,
        ]);

        $response = $templateGroupServiceMock->setUp($user);
        $this->assertEquals(false, $response);
    }
}
