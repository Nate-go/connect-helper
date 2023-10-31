<?php

namespace App\Services\ModelServices;
use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ConnectionConstant\ConnectionType;
use App\Constants\ContactConstant\ContactType;
use App\Models\Connection;
use App\Models\ConnectionUser;
use App\Models\User;

class ConnectionService extends BaseService
{
    protected $gmailTokenService;

    protected $contactService;

    public function __construct(GmailTokenService $gmailTokenService, ContactService $contactService, ) {
        $this->gmailTokenService = $gmailTokenService;
        $this->contactService = $contactService;
    }

    public function createConnectionUser(User $user, Connection $connection) {
        ConnectionUser::create([
            'user_id'=> $user->id,
            'connection_id' => $connection->id,
        ]);
    }

    private function parseEmail($input)
    {
        if (strpos($input, '<') !== false && strpos($input, '>') !== false) {
            $emailStart = strpos($input, '<') + 1;
            $emailEnd = strpos($input, '>');
            $email = substr($input, $emailStart, $emailEnd - $emailStart);
            $name = trim(substr($input, 0, $emailStart - 1));

            return ['email' => $email, 'name' => $name];
        } else {
            return ['email' => $input, 'name' => $input];
        }
    }

    public function setUp($user) {
        if (is_numeric($user)) {
            $user = User::where('id', $user)->first();
        }
        
        $service = $this->gmailTokenService->getGmailService($user);

        $messages = $service->users_messages->listUsersMessages('me', ['labelIds' => 'SENT']);

        $recipients = [];

        foreach ($messages->getMessages() as $message) {
            $messageData = $service->users_messages->get('me', $message->getId());
            $messageHeader = $messageData->getPayload()->getHeaders();

            foreach ($messageHeader as $header) {
                if ($header->getName() === 'To') {
                    $data = $this->parseEmail($header->getValue());
                    if(! in_array($data['email'], $recipients)) {
                        $recipients[$data['email']] = $data['name'];
                    }
                    break;
                }
            }
        }

        foreach ($recipients as $email => $name) {
            $connection = Connection::create([
                'name' => $name,
                'note' => $email,
                'type' => ConnectionType::PERSON,
                'status' => ConnectionStatus::PUBLIC
            ]);

            $this->contactService->create([
                'connection_id' => $connection->id,
                'content' => $email,
                'type' => ContactType::MAIL,
            ]);

            $this->createConnectionUser($user, $connection);
        }
    }
}