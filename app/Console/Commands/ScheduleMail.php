<?php

namespace App\Console\Commands;

use App\Services\ModelServices\EmailScheduleService;
use Illuminate\Console\Command;

class ScheduleMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(EmailScheduleService $emailScheduleService)
    {
        $emailScheduleService->run();
    }
}
