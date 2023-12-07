<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\ConnectionHistoryService;

class ConnectionHistoryController extends Controller
{
    protected $connectionHistoryService;

    public function __construct(ConnectionHistoryService $connectionHistoryService)
    {
        $this->connectionHistoryService = $connectionHistoryService;
    }

    public function updateConnection(string $connectionId)
    {
        $result = $this->connectionHistoryService->updateConnectionHistories($connectionId);

        return response()->json([
            'message' => $result ? 'Update connection history successfull' : 'Update connection history fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
