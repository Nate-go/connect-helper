<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
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
        if (! $result) {
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

    public function addMembers(string $id, Request $request)
    {
        $result = $this->scheduleService->addMembers($id, $request->get('userIds') ?? [], $request->get('contactIds') ?? []);

        return response()->json([
            'message' => $result ? 'Add member to schedule successfull' : 'Add member to schedule fail, You can not add member to someone else\'s schedules',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function deleteMembers(string $id, Request $request)
    {
        $result = $this->scheduleService->deleteMembers($id, $request->get('userIds') ?? [], $request->get('contactIds') ?? []);

        return response()->json([
            'message' => $result ? 'Delete member from schedule successfull' : 'Delete member from schedule fail, You can not add member to someone else\'s schedules',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }

    public function publish(string $id)
    {
        $result = $this->scheduleService->publishSchedule($id);

        return response()->json([
            'message' => $result ? 'Publish schedule successfull' : 'Publish schedule fail, You can not add member to someone else\'s schedules',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
