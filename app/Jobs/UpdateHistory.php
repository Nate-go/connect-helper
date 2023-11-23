<?php

namespace App\Jobs;

use App\Services\ModelServices\ConnectionHistoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $mailContact;

    protected $service;

    public function __construct($user, $mailContact, $service)
    {
        $this->user = $user;
        $this->mailContact = $mailContact;
        $this->service = $service;
    }


    public function handle(ConnectionHistoryService $connectionHistoryService): void
    {
        $connectionHistoryService->setUp($this->user, $this->mailContact, $this->service);
    }
}
