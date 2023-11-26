<?php

namespace App\Services\ModelServices;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\ScheduleContact;
use App\Models\ScheduleUser;

class ScheduleService extends BaseService
{
    public function __construct(Schedule $schedule)
    {
        $this->model = $schedule;
    }

    public function get($user, $from, $to) {
        return $user->hasSchedules->where('started_at', '>=', $from)->where('finished_at', '<=', $to)
        ->merge($user->schedules->where('started_at', '>=', $from)->where('finished_at', '<=', $to));
    }

    public function show($id) {
        $schedule = $this->model->where('id', $id)->first();

        if(!$schedule) return false;

        return new ScheduleResource($schedule);
    }

    public function store($input) {
        $userIds = $input['userIds'];
        $contactIds = $input['contactIds'];

        $schedule = $this->create($input);

        if(!$schedule) return false;

        foreach($userIds as $userId) {
            ScheduleUser::create([
                'schedule_id' => $schedule->id,
                'user_id' => $userId
            ]);
        }

        foreach($contactIds as $contactId) {
            ScheduleContact::create([
                'schedule_id' => $schedule->id,
                'contact_id' => $contactId
            ]);
        }

        return true;
    }
}