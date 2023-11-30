<?php

namespace App\Jobs;

use App\Services\ModelServices\ConnectionService;
use App\Services\ModelServices\TagService;
use App\Services\ModelServices\TemplateGroupService;
use Illuminate\Bus\Queueable;
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

    public function handle(ConnectionService $connectionService, TemplateGroupService $templateGroupService, TagService $tagService): void
    {
        $tagService->setUp($this->user);
        $connectionService->setUp($this->user);
        $templateGroupService->setUp($this->user);
    }
}
