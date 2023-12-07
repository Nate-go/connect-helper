<?php

namespace Tests\Unit\Services\ModelService\TemplateServiceTest;

use App\Models\Template;
use App\Services\ModelServices\TemplateService;
use Tests\Unit\BaseTest;

class ShowTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $template = Template::create([
            'name' => 'name',
        ]);

        $templateServiceMock = $this->getMockService(TemplateService::class, [], [
            new Template(),
        ]);
        $response = $templateServiceMock->show($template->id);
        $this->assertIsObject($response);
    }

    public function testNotFound()
    {
        $templateServiceMock = $this->getMockService(TemplateService::class, [], [
            new Template(),
        ]);
        $response = $templateServiceMock->show(1);
        $this->assertFalse($response);
    }
}
