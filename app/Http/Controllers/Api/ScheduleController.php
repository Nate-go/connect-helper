<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateFormRequests\StoreTemplateGroupFormRequest;
use App\Services\ModelServices\ScheduleService;
use App\Services\ModelServices\TemplateGroupService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService) {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        return response()->json($this->scheduleService->get(auth()->user(), $request->get('from'), $request->get('to')), StatusResponse::SUCCESS);
    }

    public function store(Request $request)
    {
        $result = $this->scheduleService->store(array_merge($request->all(), [
            'user_id' => auth()->user()->id,
        ]));
        return response()->json([
            'message' => $result ? 'Create schedule successfull' : 'Create schedule fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function show(string $id)
    {
        $result = $this->scheduleService->show($id);
        if (!$result) {
            return response()->json([
                'message' => 'Can not find out this schedule',
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
        $result = $this->scheduleService->update($request->get('ids') ?? [], $request->get('data') ?? []);
        return response()->json([
            'message' => $result ? 'Update schedule successfull' : 'Update schedule fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function delete(Request $request)
    {
        $result = $this->scheduleService->delete($request->get('ids') ?? []);
        return response()->json([
            'message' => $result ? 'Delete schedule successfull' : 'Delete schedule fail, You can not delete someone else\'s templates',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
