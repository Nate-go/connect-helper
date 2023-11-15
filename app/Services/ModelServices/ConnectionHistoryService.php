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
        $query = "{$contact->content}";
        try {
            $messages = $service->users_messages->listUsersMessages('me', ['q' => $query])->getMessages();
            foreach ($messages as $message) {
                $messageDetail = $service->users_messages->get('me', $message->getId());
                $headers = $messageDetail->getPayload()->getHeaders();
                $sentTime = null;
                $type = ConnectionHistoryType::SEND;
                foreach ($headers as $header) {
                    if ($header->name == 'Date') {
                        $sentTime = Carbon::parse($header->value)->toDateTime()->format('Y-m-d H:i:s');
                        break;
                    }
                    if ($header->name == 'From' and stripos($header->value, $contact->content)) {
                        $type = ConnectionHistoryType::RECEIVE;
                    }
                }

                $history = $this->model->where('user_id', $user->id)->where('contact_id', $contact->id)->where('contacted_at', $sentTime)->where('type', $type)->first();
                if ($history) {
                    continue;
                }

                $this->model->create([
                    'user_id' => $user->id,
                    'contact_id' => $contact->id,
                    'contacted_at' => $sentTime,
                    'type' => $type
                ]);
            }

        } catch (\Exception $e) {

        }
    }
}