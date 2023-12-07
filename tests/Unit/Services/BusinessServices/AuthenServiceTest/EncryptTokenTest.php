<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;

class EncryptTokenTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $input = [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ];
        $authenServiceMock = $this->getMockService(AuthenService::class);

        $response = $authenServiceMock->encryptToken($input);
        $this->assertIsString($response);
    }
}
