<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequests\StoreContactFormRequest;
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

    public function create()
    {
        //
    }

    public function store(StoreContactFormRequest $request)
    {
        $result = $this->contactService->create($request->all());
        return response()->json([
            'message' => $result ? 'Create contact successfull' : 'Create contact fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $result = $this->contactService->update([$id], $request->all());
        return response()->json([
            'message' => $result ? 'Update contact successfull' : 'Update contact fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function destroy(string $id)
    {
        $result = $this->contactService->delete($id);
        return response()->json([
            'message' => $result ? 'Delete contact successfull' : 'Delete contact fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
