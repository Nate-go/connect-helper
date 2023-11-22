<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateFormRequests\StoreTemplateFormRequest;
use App\Services\ModelServices\TemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    protected $templateService;

    public function __construct(TemplateService $templateService) {
        $this->templateService = $templateService;
    }

    public function index() {
        return response()->json($this->templateService->getUseableTemplate(), StatusResponse::SUCCESS);
    }

    public function store(StoreTemplateFormRequest $request)
    {
        $result = $this->templateService->create($request->all());
        return response()->json([
            'message' => $result ? 'Create template successfull' : 'Create template fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function show(string $id)
    {
        $result = $this->templateService->show($id);
        if (!$result) {
            return response()->json([
                'message' => 'Can not find out this template',
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
        $result = $this->templateService->update([$id], $request->all());
        return response()->json([
            'message' => $result ? 'Update template successfull' : 'Update template fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function delete(string $id)
    {
        $result = $this->templateService->delete([$id]);
        return response()->json([
            'message' => $result ? 'Delete template successfull' : 'Delete template fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
