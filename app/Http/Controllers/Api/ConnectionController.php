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


    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {

    }


    public function update(Request $request)
    {
        return $this->connectionService->update($request->get('ids') ?? [], $request->get('data') ?? []);
    }

    public function destroy(Request $request)
    {
        $this->connectionService->delete($request->get('ids') ?? []);
    }

    public function merge(Request $request) {
        $result = $this->connectionService->merge($request->get('ids') ?? [], $request->get('main') ?? null);
        return response()->json([
            'message'=> $result ? 'Merge connection successfull' : 'Merge connection fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function addTags(Request $request) {
        $result = $this->connectionService->addTagsToConnections($request->get('tagIds') ?? [], $request->get('connectionIds') ?? []);
        return response()->json([
            'message' => $result ? 'Add tags successfully' : 'Add tags fail'
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function deleteTags(Request $request)
    {
        $result = $this->connectionService->deleteTagsToConnections($request->get('tagIds') ?? [], $request->get('connectionIds') ?? []);
        return response()->json([
            'message' => $result ? 'Delete connection tags successfully' : 'Delete connection tags fail'
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
