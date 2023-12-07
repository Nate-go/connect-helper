<?php

namespace Tests\Unit\Services\ModelService\ConnectionServiceTest;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Models\Connection;
use App\Models\ConnectionTag;
use App\Models\Tag;
use App\Services\ModelServices\ConnectionService;
use Tests\Unit\BaseTest;

class DeleteTagsToConnectionsTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $connection = Connection::create([
            'name' => '',
            'note' => '',
            'status' => ConnectionStatus::PUBLIC,
        ]);

        $tag = Tag::create([
            'name' => 'name',
        ]);

        ConnectionTag::create([
            'connection_id' => $connection->id,
            'tag_id' => $tag->id,
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $response = $connectionServiceMock->deleteTagsToConnections([$tag->id], [$connection->id]);
        $this->assertEquals(true, $response);
    }

    public function testConnectionsEmpty()
    {
        $tag = Tag::create([
            'name' => 'name',
        ]);

        $connectionServiceMock = $this->getMockService(ConnectionService::class);
        $response = $connectionServiceMock->deleteTagsToConnections([$tag->id], []);
        $this->assertEquals(false, $response);
    }
}
