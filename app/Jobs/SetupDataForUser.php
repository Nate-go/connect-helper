<?php

namespace App\Jobs;

use App\Services\ModelServices\ConnectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetupDataForUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;


    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle(ConnectionService $connectionService): void
    {
        $connectionService->setUp($this->user);
    }
}
