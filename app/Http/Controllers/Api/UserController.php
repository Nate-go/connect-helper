<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UltiFormRequests\InvitesFormRequest;
use App\Services\ModelServices\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(['data' => $this->userService->getEnterpriseEmployee(auth()->user(), $request->all())], StatusResponse::SUCCESS);
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
    public function destroy(Request $request)
    {
        $result = $this->userService->delete($request->get('ids') ?? []);

        return response()->json([
            'message' => $result ? 'Delete user successfull' : 'Delete user fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function getCoworkers()
    {
        return response()->json($this->userService->getCoworkers(auth()->user()), StatusResponse::SUCCESS);
    }

    public function invites(InvitesFormRequest $request)
    {
        $result = $this->userService->invites($request->get('emails'));

        return response()->json([
            'message' => $result ? 'Send invite mail successfully' : 'Send invite mail fail',
            'data' => $result,
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function getDashboard()
    {
        return response()->json($this->userService->getDashboard(auth()->user()), StatusResponse::SUCCESS);
    }
}
