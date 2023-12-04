<?php

namespace App\Services\ModelServices;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ContactConstant\ContactType;
use App\Http\Resources\ConnectionResource;
use App\Http\Resources\ContactDataResource;
use App\Http\Resources\ShowConnectionResource;
use App\Models\Connection;
use App\Models\ConnectionTag;
use App\Models\ConnectionUser;
use App\Models\Schedule;
use App\Models\User;
use Google\Service\Calendar\ConferenceData as Google_Service_Calendar_ConferenceData;
use Google\Service\Calendar\ConferenceSolutionKey as Google_Service_Calendar_ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest as Google_Service_Calendar_CreateConferenceRequest;
use Google\Service\Calendar\EventReminders as Google_Service_Calendar_EventReminders;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Exception;

class ConnectionService extends BaseService
{
    protected $gmailTokenService;

    protected $contactService;

    protected $enterpriseService;

    protected $connectionHistoryService;

    public function __construct(
        Connection $connection,
        GmailTokenService $gmailTokenService,
        ContactService $contactService,
        EnterpriseService $enterpriseService,
        ConnectionHistoryService $connectionHistoryService
    ) {
        $this->model = $connection;
        $this->gmailTokenService = $gmailTokenService;
        $this->contactService = $contactService;
        $this->enterpriseService = $enterpriseService;
        $this->connectionHistoryService = $connectionHistoryService;
    }

    public function getConnections($input)
    {
        $tags = $input['tags'] ?? [];
        $statuses = $input['statuses'] ?? [];
        $search = $input['search'] ?? '';
        $user = auth()->user();
        $query = $this->model->with(['tags' => function($query) use($user) {
            $query->where('user_id', $user->id);
        }, 'users', 'user'])->enterpriseConnection()->where('name', 'LIKE', '%'.$search.'%')->tagFilter($tags)->statusFilter($statuses);
        $data = $this->getAll($input, $query);
        $data['items'] = ConnectionResource::collection($data['items']);

        return $data;
    }

    public function createConnectionUser($userId, $connectionId)
    {
        ConnectionUser::create([
            'user_id' => $userId,
            'connection_id' => $connectionId,
        ]);
    }

    public function update($ids, $data)
    {
        if (isset($data['status'])) {
            return $this->model->whereIn('id', $ids)->whereNot('status', ConnectionStatus::COWORKER)->update($data);
        }

        return parent::update($ids, $data);
    }

    public function deleteConnectionUser($userId, $connectionId)
    {
        ConnectionUser::where('user_id', $userId)->where('connection_id', $connectionId)->delete();
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

    public function setUp($user)
    {
        if (is_numeric($user)) {
            $user = User::where('id', $user)->first();
        }

        $service = $this->gmailTokenService->getGmailService($user);

        $this->setConnectionUser($user, $user->name, $user->email, ConnectionStatus::COWORKER);

        $messages = $service->users_messages->listUsersMessages('me', ['labelIds' => 'SENT']);
        $recipients = [];

        foreach ($messages->getMessages() as $message) {
            $messageData = $service->users_messages->get('me', $message->getId());
            $messageHeader = $messageData->getPayload()->getHeaders();

            foreach ($messageHeader as $header) {
                if ($header->getName() === 'To') {
                    $data = $this->parseEmail($header->getValue());
                    if (! in_array($data['email'], $recipients)) {
                        $recipients[strtolower($data['email'])] = $data['name'];
                    }
                    break;
                }
            }
        }

        foreach ($recipients as $email => $name) {
            $this->setConnectionUser($user, $name, $email);
        }

        $this->setUpConnectionHistory($user, $service);
    }

    private function setConnectionUser($user, $name, $email, $status = ConnectionStatus::PRIVATE)
    {
        $connection = $this->create([
            'name' => $name,
            'note' => $email,
            'status' => $status,
            'user_id' => $user->id,
            'enterprise_id' => $user->enterprise_id,
        ]);

        $contact = $this->contactService->create([
            'connection_id' => $connection->id,
            'content' => $email,
            'type' => ContactType::MAIL,
            'title' => 'Default',
        ]);

        $connection->update([
            'contact_id' => $contact->id
        ]);

        $this->createConnectionUser($user->id, $connection->id);
    }

    public function merge($ids, $main)
    {
        if (count($ids) < 2 or ! $main) {
            return false;
        }

        $connections = $this->model->whereIn('id', $ids)->get();

        foreach ($connections as $connection) {
            if ($connection->status === ConnectionStatus::COWORKER or $connection->user_id !== auth()->user()->id) {
                return false;
            }
        }

        foreach ($connections as $connection) {
            if ($main == $connection->id) {
                continue;
            }

            $contacts = $connection->contacts;

            foreach ($contacts as $contact) {
                $contact->connection_id = $main;
                $contact->save();
            }

            $connection->delete();
        }

        return true;
    }

    public function addTagsToConnections($tagIds, $connectionIds)
    {
        if (count($tagIds) == 0 or count($connectionIds) == 0) {
            return false;
        }

        foreach ($connectionIds as $connectionId) {
            foreach ($tagIds as $tagId) {
                $connection_tags = ConnectionTag::where('connection_id', $connectionId)->where('tag_id', $tagId)->first();
                if (! $connection_tags) {
                    ConnectionTag::create([
                        'connection_id' => $connectionId,
                        'tag_id' => $tagId,
                    ]);
                }
            }
        }

        return true;
    }

    public function deleteTagsToConnections($tagIds, $connectionIds)
    {
        if (count($tagIds) == 0 or count($connectionIds) == 0) {
            return false;
        }

        foreach ($connectionIds as $connectionId) {
            foreach ($tagIds as $tagId) {
                $connection_tags = ConnectionTag::where('connection_id', $connectionId)->where('tag_id', $tagId)->first();
                if ($connection_tags) {
                    $connection_tags->delete();
                }
            }
        }

        return true;
    }

    public function setUpConnectionHistory($user, $service)
    {
        $connections = $user->connections;
        foreach ($connections as $connection) {
            $mailContacts = $connection->mailContacts;
            foreach ($mailContacts as $mailContact) {
                $this->connectionHistoryService->setUp($user, $mailContact, $service);
            }
        }
    }

    public function createConnection($input)
    {
        $tagIds = $input['tagIds'];
        $data = $input['data'];

        $connection = $this->create(array_merge(
            $data,
            [
                'user_id' => auth()->user()->id,
                'enterprise_id' => auth()->user()->enterprise_id,
            ]
        ));

        if (! $connection) {
            return false;
        }

        $this->createConnectionUser(auth()->user()->id, $connection->id);

        $this->addTagsToConnections($tagIds, [$connection->id]);

        return true;
    }

    public function showConnection($id)
    {
        $connection = $this->model->with(['user', 'tags', 'users'])->where('id', $id)->first();

        if (! $connection) {
            return false;
        }

        return new ShowConnectionResource($connection);
    }

    public function editConnection($id, $input)
    {
        $connection = $this->model->where('id', $id)->first();

        if (! $connection) {
            return false;
        }

        $name = $input['name'];
        $note = $input['note'];
        $ownerId = $input['ownerId'];
        $status = $input['status'];
        $tagIds = $input['tagIds'];
        $contact_id = $input['contact_id'];

        $connection->update([
            'name' => $name,
            'note' => $note,
            'status' => $status,
            'user_id' => $ownerId,
            'contact_id' => $contact_id
        ]);

        $currentTagIds = $connection->tags()->pluck('tags.id')->toArray();
        $this->deleteTagsToConnections(array_diff($currentTagIds, $tagIds), [$id]);
        $this->addTagsToConnections(array_diff($tagIds, $currentTagIds), [$id]);

        return true;
    }

    public function getContacts($connectionId)
    {
        $connection = $this->model->with(['histories' => [
            'contact', 
            'user'
        ], 'contacts'])->where('id', $connectionId)->first();

        if (! $connection) {
            return false;
        }

        return new ContactDataResource($connection);
    }

    public function addUserConnections($connectionIds, $userIds)
    {
        if (count($userIds) == 0 or count($connectionIds) == 0) {
            return false;
        }

        $connections = $this->model->whereIn('id', $connectionIds)->get();
        foreach ($connections as $connection) {
            $addIds = array_diff($userIds, $this->getColumn($connection->users));
            foreach ($addIds as $id) {
                $this->createConnectionUser($id, $connection->id);
            }
        }

        return true;
    }

    public function deleteUserConnections($connectionIds, $userIds)
    {
        if (count($userIds) == 0 or count($connectionIds) == 0) {
            return false;
        }

        $connections = $this->model->whereIn('id', $connectionIds)->get();
        foreach ($connections as $connection) {
            $deleteIds = array_intersect($userIds, $this->getColumn($connection->users));
            foreach ($deleteIds as $id) {
                $this->deleteConnectionUser($id, $connection->id);
            }
        }

        return true;
    }

    public function getUserConnections()
    {
        return auth()->user()->connections->map(function ($connection) {
            return [
                'id' => $connection->id,
                'name' => $connection->name,
                'note' => $connection->note,
                'contacts' => $connection->mailContacts,
                'contact_id' => $connection->contact_id
            ];
        });
    }

    public function test()
    {
        $user = User::where('id', 1)->first();
        $service = $this->gmailTokenService->getCalendarService($user);
        $schedule = Schedule::where('id', 1)->first();
        $emails = ['xayvier01@gmail.com'];
        $newEmail = [];
        foreach ($emails as $email) {
            $newEmail[] = [
                'email' => $email,
            ];
        }
        $conference = new Google_Service_Calendar_ConferenceData();
        $conferenceRequest = new Google_Service_Calendar_CreateConferenceRequest();
        $conferenceSolutionKey = new Google_Service_Calendar_ConferenceSolutionKey();
        $conferenceSolutionKey->setType('hangoutsMeet');
        $conferenceRequest->setRequestId($user->email.time());
        $conferenceRequest->setConferenceSolutionKey($conferenceSolutionKey);
        $conference->setCreateRequest($conferenceRequest);

        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('email');
        $reminder->setMinutes(30);

        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides([$reminder]);

        return json_encode([
            'summary' => $schedule->title,
            'description' => $schedule->content,
            'start' => [
                'dateTime' => $this->customDate($schedule->started_at),
                'timeZone' => 'Asia/Ho_Chi_Minh',
            ],
            'end' => [
                'dateTime' => $this->customDate($schedule->finished_at),
                'timeZone' => 'Asia/Ho_Chi_Minh',
            ],
            'attendees' => $newEmail,
            'conferenceData' => $conference,
        ]);

        $event = new Google_Service_Calendar_Event([
            'summary' => $schedule->title,
            'description' => $schedule->content,
            'start' => [
                'dateTime' => $this->customDate($schedule->started_at),
                'timeZone' => 'Asia/Ho_Chi_Minh',
            ],
            'end' => [
                'dateTime' => $this->customDate($schedule->finished_at),
                'timeZone' => 'Asia/Ho_Chi_Minh',
            ],
            'attendees' => $newEmail,
            'conferenceData' => $conference,
        ]);

        $event->setReminders($reminders);

        $calendarId = 'primary';

        try {
            $createEvent = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
            $meetingLink = $createEvent->getHangoutLink();

            return $meetingLink;
        } catch (Google_Service_Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($ids)
    {
        $ownConnectionIds = $this->getColumn(auth()->user()->ownConnections);

        if (! $this->includesAll($ids, $ownConnectionIds)) {
            return false;
        }

        return $this->model->destroy($ids);
    }
}
