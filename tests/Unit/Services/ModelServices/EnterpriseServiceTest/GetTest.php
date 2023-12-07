<?php

namespace Tests\Unit\Services\ModelService\EnterpriseServiceTest;

use App\Models\Enterprise;
use App\Services\ModelServices\EnterpriseService;
use Tests\Unit\BaseTest;

class GetTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {

        $enterpriseServiceMock = $this->getMockService(EnterpriseService::class, [], [
            new Enterprise(),
        ]);
        $response = $enterpriseServiceMock->get([]);
        $this->assertIsArray($response);
    }
}
