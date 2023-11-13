<?php

namespace App\Services\ModelServices;
use App\Constants\ConnectionHistoryConstant\ConnectionHistoryType;
use App\Models\ConnectionHistory;
use Carbon\Carbon;

class ConnectionHistoryService extends BaseService
{
    public function __construct(ConnectionHistory $connectionHistory) {
        $this->model = $connectionHistory;
    }

    public function setUp($user, $contact, $service) {
        $this->setSentMail($user, $contact, $service);
        $this->setReceiveMail($user, $contact, $service);
    }

    public function setSentMail($user, $contact, $service) {
        $query = "from:($user->email) to:($contact->content)";
        try {
            $messages = $service->users_messages->listUsersMessages('me', ['q' => $query])->getMessages();
            foreach ($messages as $message) {
                $messageDetail = $service->users_messages->get('me', $message->getId());

                $headers = $messageDetail->getPayload()->getHeaders();
                $sentTime = null;

                foreach ($headers as $header) {
                    if ($header->name == 'Date') {
                        $sentTime = Carbon::parse($header->value)->toDateTime()->format('Y-m-d H:i:s');
                        break;
                    }
                }

                $this->model->create([
                    'user_id'=> $user->id,
                    'contact_id' => $contact->id,
                    'contacted_at' => $sentTime,
                    'type' => ConnectionHistoryType::SEND
                ]);
            }

        } catch (\Exception $e) {
        }
    }

    public function setReceiveMail($user, $contact, $service)
    {
        $query = "from:($contact->content) to:($user->email)";
        try {
            $messages = $service->users_messages->listUsersMessages('me', ['q' => $query])->getMessages();
            foreach ($messages as $message) {
                $messageDetail = $service->users_messages->get('me', $message->getId());

                $headers = $messageDetail->getPayload()->getHeaders();
                $sentTime = null;

                foreach ($headers as $header) {
                    if ($header->name == 'Date') {
                        $sentTime = Carbon::parse($header->value)->toDateTime()->format('Y-m-d H:i:s');
                        break;
                    }
                }

                $this->model->create([
                    'user_id' => $user->id,
                    'contact_id' => $contact->id,
                    'contacted_at' => $sentTime,
                    'type' => ConnectionHistoryType::RECEIVE
                ]);
            }

        } catch (\Exception $e) {
            
        }
    }
}