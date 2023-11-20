<?php

namespace App\Jobs;

use App\Services\ModelServices\GmailTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailFromUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;

    protected $subject;

    protected $content;

    protected $user;

    public function __construct($type, $subject, $content, $user)
    {
        $this->type = $type;
        $this->subject = $subject;
        $this->content = $content;
        $this->user = $user;
    }


    public function handle(GmailTokenService $gmailTokenService): void
    {
        $gmailTokenService->sendMail($this->type, $this->subject, $this->content, $this->user);
    }
}
