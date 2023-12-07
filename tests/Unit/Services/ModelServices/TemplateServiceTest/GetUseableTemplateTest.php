<?php

namespace Tests\Unit\Services\ModelService\TemplateServiceTest;

use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\Template;
use App\Models\User;
use App\Services\ModelServices\TemplateService;
use Tests\Unit\BaseTest;

class GetUseableTemplateTest extends BaseTest
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

        $templateServiceMock = $this->getMockService(TemplateService::class, [], [
            new Template(),
        ]);
        $this->be($user);
        $response = $templateServiceMock->getUseableTemplate();
        $this->assertIsObject($response);
    }
}
