<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\ConnectionService;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    protected $connectionService;

    public function __construct(ConnectionService $connectionService) {
        $this->connectionService = $connectionService;
    }

    public function index(Request $request)
    {
        $data = $this->connectionService->getConnections($request->all());
        return response()->json([
            'message' => 'Get connection successfully',
            'data' => $data
        ], StatusResponse::SUCCESS);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
