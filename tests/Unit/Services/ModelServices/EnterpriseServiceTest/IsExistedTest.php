<?php

namespace Tests\Unit\Services\ModelService\EnterpriseServiceTest;

use App\Models\Enterprise;
use App\Services\ModelServices\EnterpriseService;
use Tests\Unit\BaseTest;

class IsExistedTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExisted()
    {
        Enterprise::create([
            'name' => 'new',
        ]);

        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class, [], [
            new Enterprise(),
        ]);
        $response = $enterpriseServiceMock->isExisted('new');
        $this->assertTrue($response);
    }

    public function testNotExisted()
    {
        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class, [], [
            new Enterprise(),
        ]);
        $response = $enterpriseServiceMock->isExisted('new');
        $this->assertFalse($response);
    }
}
