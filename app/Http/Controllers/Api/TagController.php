<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService) {
        $this->tagService = $tagService;
    }
    public function index()
    {
        return $this->tagService->getAllTags();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $result = $this->tagService->create($request->all());
        $message = "This tag name has been exist";
        if($result) {
            $message = "Create tag successfully";
        };
        return response()->json([
            "message" => $message
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->tagService->update($id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        return $this->tagService->delete($id);
    }
}
