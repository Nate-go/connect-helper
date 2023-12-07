<?php

namespace Tests\Unit\Services\ModelService\TagServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\Tag;
use App\Models\User;
use App\Services\ModelServices\TagService;
use Tests\Unit\BaseTest;

class CreateTest extends BaseTest
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
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        $data = [
            'name' => 'name',
        ];

        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);
        $this->be($user);
        $response = $tagServiceMock->create($data);
        $this->assertIsObject($response);
    }

    public function testHasExisted()
    {
        $user = User::create([
            'name' => 'name',
            'password' => '123',
            'role' => 0,
            'status' => UserStatus::ACTIVE,
        ]);

        Tag::create([
            'user_id' => $user->id,
            'name' => 'name',
        ]);

        $data = [
            'name' => 'name',
        ];

        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);
        $this->be($user);
        $response = $tagServiceMock->create($data);
        $this->assertFalse($response);
    }
}
