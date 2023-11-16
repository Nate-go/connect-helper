<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UltiFormRequests\InvitesFormRequest;
use App\Models\User;
use App\Services\ModelServices\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->userService->getAllOwner();
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

    public function getCoworkers() 
    {
        return response()->json($this->userService->getCoworkers(auth()->user()), StatusResponse::SUCCESS);
    }

    public function invites(InvitesFormRequest $request) {
        $result = $this->userService->invites($request->get('emails'));
        return response()->json([
            'message' => $result ? 'Send invite mail successfully' : 'Send invite mail fail'
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
