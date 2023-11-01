<?php

namespace App\Services\ModelServices;
use App\Models\Schedule;

class ScheduleService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Schedule::class);
    }
}