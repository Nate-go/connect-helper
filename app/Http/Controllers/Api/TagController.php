<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagFormRequests\StoreTagFormRequest;
use App\Services\ModelServices\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        return $this->tagService->getAllTags();
    }

    public function create()
    {

    }

    public function store(StoreTagFormRequest $request)
    {
        $this->tagService->create($request->all());

        return response()->json([
            'message' => 'Create tag successfully',
        ], StatusResponse::SUCCESS);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $result = $this->tagService->detail($id);
        if (! $result) {
            return response()->json([
                'message' => 'Can not find out this tag',
            ], StatusResponse::ERROR);
        }

        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function update(Request $request, string $id)
    {
        $result = $this->tagService->update([$id], $request->all());

        return response()->json([
            'message' => $result ? 'Update tags successfully' : 'Update tags fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function destroy(Request $request)
    {
        $result = $this->tagService->delete($request->get('ids') ?? []);

        return response()->json([
            'message' => $result ? 'Delete tags successfully' : 'Delete tags fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
