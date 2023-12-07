<?php

namespace Tests\Unit\Services\BusinessService\AuthenServiceTest;

use App\Services\BusinessServices\AuthenService;

class GetEnterpriseIdTest extends BaseAuthenServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $authenServiceMock = $this->getMockService(AuthenService::class);
        $data = 'def50200239b023a5a09ac5f459886b902b1ccc6830a0d6b9d34f696a1cf5068cf0b8edc8a4ffcf6c28f6011a4ca7ebb7e678d5b13a78b09f14c42f1a1b37fab36fbaeed755072b66ac932089b45d4a743481ce0f835e03b7914cc4082f1b8ded7f80f070eb7f4ea44fe0f223f9050b0ff85fc3d7511f3';
        $response = $authenServiceMock->getEnterpriseId($data);
        $this->assertIsString($response);
    }

    public function testFail()
    {
        $authenServiceMock = $this->getMockService(AuthenService::class);
        $data = 'def502005d0f0d843c0300c9e6be68b5d3327efc415ec7a43b9c9b43bda9df0bc4045b6e853d01d4d9655c4e0906367956819b944cdf2fd9c58e84ffc8564c5907e2d150dc9cde4e61e3ac77808c34697bb52fb9046d2403c4acd01babd3aff7c871687857514f3dd3d274d25a5d0389defc0ac99837253dadc6806d053a0685fd18a144b19b82a4cc20ac1999afd3d7fc2db43e8599';
        $response = $authenServiceMock->getEnterpriseId($data);
        $this->assertFalse($response);
    }
}
