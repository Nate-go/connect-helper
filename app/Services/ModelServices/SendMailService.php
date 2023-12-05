<?php

namespace App\Services\ModelServices;

use App\Constants\EmailScheduleConstant\EmailScheduleStatus;
use App\Constants\SendMailConstant\SendMailType;
use App\Jobs\SendMailFromUser;
use App\Models\SendMail;

class SendMailService extends BaseService
{
    protected $sendMailContactService;

    protected $emailScheduleService;

    public function __construct(SendMail $sendMail, SendMailContactService $sendMailContactService, EmailScheduleService $emailScheduleService)
    {
        $this->model = $sendMail;
        $this->sendMailContactService = $sendMailContactService;
        $this->emailScheduleService = $emailScheduleService;
    }

    public function store($input)
    {
        $user = auth()->user();
        $sendMail = $this->create([
            'user_id' => $user->id,
            'name' => $input['name'],
            'title' => $input['title'],
            'content' => $input['content'],
            'type' => $input['type'],
        ]);

        if (! $sendMail) {
            return false;
        }

        $contactIds = $input['contactIds'];

        foreach ($contactIds as $contactId) {
            $this->sendMailContactService->create([
                'send_mail_id' => $sendMail->id,
                'contact_id' => $contactId,
                'title' => $this->sendMailContactService->replaceData($input['title'], $contactId),
                'content' => $this->sendMailContactService->replaceData($input['content'], $contactId),
                'type' => $input['type'],
            ]);
        }

        $schedule = $input['schedule'] ?? null;
        if($schedule) {
            $this->emailScheduleService->create([
                'user_id' => $user->id,
                'send_mail_id' => $sendMail->id,
                'started_at' => $input['schedule']['started_at'],
                'nextTime_at' => $input['schedule']['started_at'],
                'after_second' => $input['schedule']['after_second'],
                'status' => EmailScheduleStatus::RUNNING,
                'name' => $sendMail->name
            ]);
        } else {
            $this->sendMail($sendMail->id);
        }

        return true;
    }

    public function sendMail($id)
    {
        $sendMail = $this->model->where('id', $id)->first();
        $user = $sendMail->user;

        if (! $sendMail) {
            return false;
        }

        if ($sendMail->type === SendMailType::PERSONAL) {
            $sendMailContacts = $sendMail->sendMailContacts;
            foreach ($sendMailContacts as $sendMailContact) {
                $this->sendMailContactService->sendMail($sendMailContact->id);
            }

            return true;
        }

        $emails = $this->getColumn($sendMail->contacts, 'content');
        $type = $sendMail->type === SendMailType::CC ? 'Cc: ' : 'Bcc: ';
        $subject = $sendMail->title;
        $content = $sendMail->content;

        SendMailFromUser::dispatch($type.implode(', ', $emails), $subject, $content, $user);

        return true;
    }
}
