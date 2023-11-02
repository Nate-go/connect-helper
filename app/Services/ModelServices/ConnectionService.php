<?php

namespace App\Services\ModelServices;

use App\Constants\AuthenConstant\StatusResponse;
use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ConnectionConstant\ConnectionType;
use App\Constants\ContactConstant\ContactType;
use App\Constants\UtilConstant;
use App\Http\Resources\ConnectionResource;
use App\Models\Connection;
use App\Models\ConnectionUser;
use App\Models\User;
use Request;


class ConnectionService extends BaseService
{
    protected $gmailTokenService;

    protected $contactService;

    protected $enterpriseService;
    
    public function __construct(Connection $connection, GmailTokenService $gmailTokenService, ContactService $contactService, EnterpriseService $enterpriseService) {
        $this->model = $connection;
        $this->gmailTokenService = $gmailTokenService;
        $this->contactService = $contactService;
        $this->enterpriseService = $enterpriseService;
    }

    public function getConnections($input) {
        $tags = $input["tags"] ?? [];
        $statuses = $input["statuses"] ?? [];

        $query = $this->model->enterpriseConnection()->tagFilter($tags)->statusFilter($statuses);
        $data = $this->getAll($input, $query);
        $data['items'] = ConnectionResource::collection($data['items']);
        return $data;

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
            $user = $this->getFirst($user);
        }

        $this->setConnectionUser($user, $user->name, $user->email);
        
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
            $this->setConnectionUser($user, $name, $email);
        }
    }

    private function setConnectionUser($user, $name, $email) {
        $connection = $this->create([
            'name' => $name,
            'note' => $email,
            'type' => ConnectionType::PERSON,
            'status' => ConnectionStatus::PRIVATE,
            'user_id' => $user->id,
            'enterprise_id' => $user->enterprise_id

        ]);

        $this->contactService->create([
            'connection_id' => $connection->id,
            'content' => $email,
            'type' => ContactType::MAIL,
        ]);

        $this->createConnectionUser($user, $connection);
    }

}