<?php

namespace App\Services\ModelServices;
use App\Constants\ConnectionHistoryConstant\ConnectionHistoryType;
use App\Models\Connection;
use App\Models\ConnectionHistory;
use Carbon\Carbon;

class ConnectionHistoryService extends BaseService
{
    protected $gmailTokenService;

    public function __construct(ConnectionHistory $connectionHistory, GmailTokenService $gmailTokenService) {
        $this->model = $connectionHistory;
        $this->gmailTokenService = $gmailTokenService;
    }

    public function getLastContactedAt($user, $contact)
    {
        try {
            $latestContact = $this->model
                ->where('user_id', $user->id)
                ->where('contact_id', $contact->id)
                ->whereNotNull('contacted_at')
                ->orderBy('contacted_at', 'desc')
                ->first();

            return $latestContact ? $latestContact->contacted_at : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFutureMessages($user, $contact, $service)
    {
        try {
            $query = "{$contact->content}";
            $lastContactedAt = $this->getLastContactedAt($user, $contact);
            if ($lastContactedAt) {
                $query = "{$contact->content} after:{$lastContactedAt}";
            }

            $messages = $service->users_messages->listUsersMessages('me', ['q' => $query])->getMessages();

            return $messages;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function setUp($user, $contact, $service) {
        try {
            $messages = $this->getFutureMessages($user, $contact, $service);
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
                    'type' => $type,
                    'link' => "https://mail.google.com/mail/u/0/#inbox/{$message->getId()}"
                ]);
            }

        } catch (\Exception $e) {

        }
    }

    public function updateConnectionHistories($connection) {

        if(is_numeric($connection)) {
            $connection = Connection::where('id', $connection)->first();

            if (!$connection)
                return false;
        }

        $users = $connection->users;
        $mailContacts = $connection->mailContacts;

        foreach ($users as $user) {
            $service = $this->gmailTokenService->getGmailService($user);
            foreach ($mailContacts as $mailContact) {
                $this->setUp($user, $mailContact, $service);
            }
        }

        return true;
    }

    public function updateUserHistories($user) {
        $connections = $user->connections;
        foreach($connections as $connection) {
            $this->updateConnectionHistories($connection);
        }

        return true;
    }
}