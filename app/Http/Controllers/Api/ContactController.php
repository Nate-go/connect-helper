<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService) {
        $this->contactService = $contactService;
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $result = $this->contactService->update($id, $request->all());
        return response()->json([
            'message' => $result ? 'Update contact successfull' : 'Update contact fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->contactService->delete($id);
        return response()->json([
            'message' => $result ? 'Delete contact successfull' : 'Delete contact fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
