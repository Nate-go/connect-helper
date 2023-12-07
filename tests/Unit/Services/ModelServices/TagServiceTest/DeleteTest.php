<?php

namespace Tests\Unit\Services\ModelService\TagServiceTest;

use App\Constants\UserConstant\UserStatus;
use App\Models\Tag;
use App\Models\User;
use App\Services\ModelServices\TagService;
use Tests\Unit\BaseTest;

class DeleteTest extends BaseTest
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

        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);
        $response = $tagServiceMock->delete([$tag->id]);
        $this->assertTrue($response);
    }

    public function testEmpty()
    {
        $tagServiceMock = $this->getMockService(TagService::class, [], [
            new Tag(),
        ]);

        $response = $tagServiceMock->delete([]);
        $this->assertFalse($response);
    }
}
