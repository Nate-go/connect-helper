<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\EmailScheduleService;
use Illuminate\Http\Request;

class EmailScheduleController extends Controller
{
    protected $emailScheduleService;

    public function __construct(EmailScheduleService $emailScheduleService)
    {
        $this->emailScheduleService = $emailScheduleService;
    }

    public function index(Request $request)
    {
        $data = $this->emailScheduleService->get($request->all());

        return response()->json([
            'data' => $data,
        ], StatusResponse::SUCCESS);
    }

    public function show(string $id)
    {
        $result = $this->emailScheduleService->show($id);
        if (! $result) {
            return response()->json([
                'message' => 'Can not find out this schedule mail',
            ], StatusResponse::ERROR);
        }

        return response()->json($result, StatusResponse::SUCCESS);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request)
    {
        $result = $this->emailScheduleService->update($request->get('ids') ?? [], $request->get('data') ?? []);

        return response()->json([
            'message' => $result ? 'Update email schedule successfull' : 'Update email schedule fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function delete(Request $request)
    {
        $result = $this->emailScheduleService->delete($request->get('ids') ?? []);

        return response()->json([
            'message' => $result ? 'Delete email schedule successfull' : 'Delete email schedule fail, You can not delete someone else\'s templates',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
