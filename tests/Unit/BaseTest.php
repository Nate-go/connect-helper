<?php

namespace Tests\Unit;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        
    }

    protected function getMockService($class, $method=[], $contructs=[]) {
        if($contructs === []) {
            return $this->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->onlyMethods($method)
                ->getMock();
        }

        return $this->getMockBuilder($class)
            ->setConstructorArgs($contructs)
            ->onlyMethods($method)
            ->getMock();
    }
}
