<?php

namespace App\Services\ModelServices;
use App\Constants\SendMailConstant\SendMailType;
use App\Jobs\SendMailFromUser;
use App\Models\SendMail;

class SendMailService extends BaseService
{
    protected $sendMailContactService;

    public function __construct(SendMail $sendMail, SendMailContactService $sendMailContactService) {
        $this->model = $sendMail;
        $this->sendMailContactService = $sendMailContactService;
    }

    public function store($input) {
        $sendMail = $this->create([
            'user_id' => auth()->user()->id,
            'name' => $input['name'],
            'title' => $input['title'],
            'content' => $input['content'],
            'type' => $input['type'],
        ]);

        if(!$sendMail) return false;

        $contactIds = $input['contactIds'];

        foreach($contactIds as $contactId) {
            $this->sendMailContactService->create([
                'send_mail_id' => $sendMail->id,
                'contact_id' => $contactId,
                'title' => $this->sendMailContactService->replaceData($input['title'], $contactId),
                'content' => $this->sendMailContactService->replaceData($input['content'], $contactId),
                'type' => $input['type'],
            ]);
        }

        $this->sendMail($sendMail->id);

        return true;
    }

    public function sendMail($id) {
        $user = auth()->user();
        $sendMail = $this->model->where('id', $id)->where('user_id', $user->id)->first();

        if(!$sendMail) return false;

        if($sendMail->type === SendMailType::PERSONAL) {
            $sendMailContacts = $sendMail->sendMailContacts;
            foreach($sendMailContacts as $sendMailContact) {
                $this->sendMailContactService->sendMail($sendMailContact->id);
            }
            return true;
        }

        $emails = $this->getColumn($sendMail->contacts, 'content');
        $type = $sendMail->type === SendMailType::CC ? "Cc: " : "Bcc: ";
        $subject = $sendMail->title;
        $content = $sendMail->content;

        SendMailFromUser::dispatch($type . implode(', ', $emails), $subject, $content, $user);

        return true;
    }
}