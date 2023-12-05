<?php

namespace App\Services\ModelServices;

use App\Jobs\SendMailFromUser;
use App\Models\Contact;
use App\Models\SendMailContact;

class SendMailContactService extends BaseService
{
    public function __construct(SendMailContact $sendMailContact)
    {
        $this->model = $sendMailContact;
    }

    public function replaceData($content, $contactId)
    {
        $contact = Contact::where('id', $contactId)->first();

        if (! $contact) {
            return false;
        }

        $connection = $contact->connection;
        $user = auth()->user();

        $data = [
            '@name@' => $connection->name,
            '@note@' => $connection->note,
            '@title@' => $contact->title,
            '@content@' => $contact->content,
            '@username@' => $user->name,
            '@enterprise@' => $user->enterprise->name,
        ];

        foreach ($data as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    public function sendMail($id)
    {
        $sendMailContact = $this->model->where('id', $id)->first();
        if (!$sendMailContact) {
            return false;
        }
        $user = $sendMailContact->sendMail->user;

        $contact = $sendMailContact->contact;

        $type = 'To: '.$contact->content;
        $subject = $sendMailContact->title;
        $content = $sendMailContact->content;
        SendMailFromUser::dispatch($type, $subject, $content, $user);
    }
}
