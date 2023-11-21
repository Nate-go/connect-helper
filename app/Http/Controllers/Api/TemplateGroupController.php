<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateFormRequests\StoreTemplateGroupFormRequest;
use App\Services\ModelServices\TemplateGroupService;
use Illuminate\Http\Request;

class TemplateGroupController extends Controller
{
    protected $templateGroupService;

    public function __construct(TemplateGroupService $templateGroupService) {
        $this->templateGroupService = $templateGroupService;
    }

    public function index(Request $request)
    {
        $data = $this->templateGroupService->getTemplateGroups($request->all());
        return response()->json([
            'data' => $data
        ], StatusResponse::SUCCESS);
    }

    public function store(StoreTemplateGroupFormRequest $request)
    {
        $result = $this->templateGroupService->create(array_merge($request->all(), [
            'user_id' => auth()->user()->id,
            'enterprise_id' => auth()->user()->enterprise_id
        ]));
        return response()->json([
            'message' => $result ? 'Create templateGroup successfull' : 'Create templateGroup fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function show(string $id)
    {
        $result = $this->templateGroupService->show($id);
        if (!$result) {
            return response()->json([
                'message' => 'Can not find out this templateGroup',
            ], StatusResponse::ERROR);
        }
        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $result = $this->templateGroupService->update($id, $request->all());
        return response()->json([
            'message' => $result ? 'Update templateGroup successfull' : 'Update templateGroup fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function delete(Request $request)
    {
        $result = $this->templateGroupService->delete($request->get('ids') ?? []);
        return response()->json([
            'message' => $result ? 'Delete templateGroup successfull' : 'Delete templateGroup fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
