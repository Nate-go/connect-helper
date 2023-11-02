<?php

namespace App\Services\ModelServices;
use App\Models\Schedule;

class ScheduleService extends BaseService
{
    public function __construct(Schedule $schedule)
    {
        $this->model = $schedule;
    }
}