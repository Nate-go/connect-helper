<?php

namespace Tests\Unit\Services\ModelService\TagServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\UserConstant\UserStatus;
use App\Models\Connection;
use App\Models\ConnectionTag;
use App\Models\Tag;
use App\Models\User;
use App\Services\ModelServices\TagService;
use Tests\Unit\BaseTest;

class DetailTest extends BaseTest
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

        $tag = Tag::create([
            'user_id' => $user->id,
        ]);

        $connection = Connection::create([
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        ConnectionTag::create([
            'tag_id' => $tag->id,
            'connection_id' => $connection->id,
        ]);

        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);
        $response = $tagServiceMock->detail($tag->id);
        $this->assertIsArray($response);
    }

    public function testNotExisted()
    {
        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);

        $response = $tagServiceMock->detail(1);
        $this->assertFalse($response);
    }
}
