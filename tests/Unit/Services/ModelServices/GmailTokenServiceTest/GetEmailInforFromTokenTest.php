<?php

namespace Tests\Unit\Services\ModelService\GmailTokenServiceTest;

use App\Models\GmailToken;
use App\Services\ModelServices\GmailTokenService;
use Tests\Unit\BaseTest;

class GetEmailInforFromTokenTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExisted()
    {
        $data = ['name' => 'name', 'age' => 'age'];
        $token = 'header.'.base64_encode(json_encode($data)).'.signature';
        $gmailTokenServiceMock = $this->getMockService(GmailTokenService::class, [], [
            new GmailToken(),
        ]);
        $response = $gmailTokenServiceMock->getEmailInforFromToken($token);
        $this->assertEquals($data, $response);
    }
}
