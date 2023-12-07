<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;

class DecryptTokenTest extends BaseAuthenServiceTest
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
        $data = 'def502005d0f0d843c0300c9e6be68b5d3327efc415ec7a43b9c9b43bda9df0bc4045b6e853d01d4d9655c4e0906367956819b944cdf2fd9c58e84ffc8564c5907e2d150dc9cde4e61e3ac77808c34697bb52fb9046d2403c4acd01babd3aff7c871687857514f3dd3d274d25a5d0389defc0ac99837253dadc6806d053a0685fd18a144b19b82a4cc20ac1999afd3d7fc2db43e8599';
        $authenServiceMock = $this->getMockService(AuthenService::class);

        $response = $authenServiceMock->decryptToken($data);
        $this->assertEquals($input, $response);
    }
}
