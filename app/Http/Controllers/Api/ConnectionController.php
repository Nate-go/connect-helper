<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConnectionFormRequests\EditConnectionFormRequest;
use App\Http\Requests\ConnectionFormRequests\StoreConnectionFormRequest;
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
            'data' => $data
        ], StatusResponse::SUCCESS);
    }


    public function store(StoreConnectionFormRequest $request)
    {
        $result = $this->connectionService->createConnection($request->all());
        return response()->json([
            'message' => $result ? 'Create connection successfull' : 'Create connection fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function show(string $id)
    {
        $result = $this->connectionService->showConnection($id);
        if(!$result) {
            return response()->json([
                'message' => 'Can not find out this connection',
            ], StatusResponse::ERROR);
        }
        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function edit(string $id, EditConnectionFormRequest $request) {
        $result = $this->connectionService->editConnection($id, $request->all());
        return response()->json([
            'message' => $result ? 'Update connection successfull' : 'Update connection fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }


    public function update(Request $request)
    {
        $result =  $this->connectionService->update($request->get('ids') ?? [], $request->get('data') ?? []);
        return response()->json([
            'message' => $result ? 'Update connection successfull' : 'Update connection fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function destroy(Request $request)
    {
        $result = $this->connectionService->delete($request->get('ids') ?? []);
        return response()->json([
            'message' => $result ? 'Delete connection successfull' : 'Delete connection fail, You can not delete someone else\'s connections',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
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

    public function getContacts(string $connectionId) {
        $result = $this->connectionService->getContacts($connectionId);
        if (!$result) {
            return response()->json([
                'message' => 'Can not find out this connection',
            ], StatusResponse::ERROR);
        }
        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function addUserConnections(Request $request) {
        $result = $this->connectionService->addUserConnections($request->get('connectionIds') ?? [], $request->get('userIds') ?? []);
        return response()->json([
            'message' => $result ? 'Add user to connection successfully' : 'Add user to connection fail'
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function deleteUserConnections(Request $request)
    {
        $result = $this->connectionService->deleteUserConnections($request->get('connectionIds') ?? [], $request->get('userIds') ?? []);
        return response()->json([
            'message' => $result ? 'Delete user to connection successfully' : 'Delete user to connection fail'
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function getUserConnections() {
        return response()->json($this->connectionService->getUserConnections(), StatusResponse::SUCCESS);
    }
}
