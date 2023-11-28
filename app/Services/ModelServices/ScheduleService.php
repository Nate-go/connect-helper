<?php

namespace App\Services\ModelServices;
use App\Constants\ScheduleConstant\ScheduleClassification;
use App\Constants\ScheduleConstant\ScheduleStatus;
use App\Constants\ScheduleConstant\ScheduleType;
use App\Http\Resources\ScheduleResource;
use App\Jobs\SendMailFromUser;
use App\Models\Schedule;
use App\Models\ScheduleContact;
use App\Models\ScheduleUser;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use View;
use Google\Service\Calendar\ConferenceData as Google_Service_Calendar_ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest as Google_Service_Calendar_CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey as Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_EventReminder;
use Google\Service\Calendar\EventReminders as Google_Service_Calendar_EventReminders;
use Google_Service_Exception;

class ScheduleService extends BaseService
{
    protected $gmailTokenService;

    public function __construct(Schedule $schedule, GmailTokenService $gmailTokenService)
    {
        $this->model = $schedule;
        $this->gmailTokenService = $gmailTokenService;
    }

    public function get($user, $from, $to) {
        return $user->hasSchedules->where('started_at', '>=', $from)->where('finished_at', '<=', $to)
        ->merge($user->schedules->where('started_at', '>=', $from)->where('finished_at', '<=', $to));
    }

    public function show($id) {
        $schedule = $this->model->where('id', $id)->first();

        if(!$schedule) return false;

        return new ScheduleResource($schedule);
    }

    public function store($input) {
        $userIds = $input['userIds'];
        $contactIds = $input['contactIds'];

        $schedule = $this->create($input);

        if(!$schedule) return false;

        foreach($userIds as $userId) {
            ScheduleUser::create([
                'schedule_id' => $schedule->id,
                'user_id' => $userId
            ]);
        }

        foreach($contactIds as $contactId) {
            ScheduleContact::create([
                'schedule_id' => $schedule->id,
                'contact_id' => $contactId
            ]);
        }

        if($schedule->status === ScheduleStatus::PUBLISH) {
            $schedule->status = ScheduleStatus::UNPUBLISH;
            $schedule->save();
            $this->publishSchedule($schedule->id);
        }

        return true;
    }

    public function publishSchedule($id) {
        $schedule = $this->model->where('id', $id)->first();

        if(!$schedule or $schedule->status === ScheduleStatus::PUBLISH) return false;
        $emails = array_merge($this->getColumn($schedule->users, 'email'), $this->getColumn($schedule->contacts, 'content'));
        $schedule->status = ScheduleStatus::PUBLISH;
        $schedule->save();
        $this->createSchedule($schedule, $emails);

        $this->invite($schedule, $emails, auth()->user());
        return true;
    }

    public function addMembers($id, $userIds, $contactIds) {

        $schedule = $this->model->where('id', $id)->first();
        if(!$schedule or $schedule->status === ScheduleStatus::PUBLISH) return false;
        $users = $schedule->users;
        $addUsers = array_diff($userIds, $this->getColumn($users));
        foreach($addUsers as $userId) {
            ScheduleUser::create([
                'schedule_id' => $schedule->id,
                'user_id' => $userId
            ]);
        }

        $contacts = $schedule->contacts;
        $addContacts = array_diff($contactIds, $this->getColumn($contacts));
        foreach ($addContacts as $contactId) {
            ScheduleContact::create([
                'schedule_id' => $schedule->id,
                'contact_id' => $contactId
            ]);
        }

        return true;
    }

    public function deleteMembers($id, $userIds, $contactIds) {
        $schedule = $this->model->where('id', $id)->first();
        if (!$schedule or $schedule->status === ScheduleStatus::PUBLISH)
            return false;
        $users = $schedule->users;
        $deleteUsers = array_intersect($userIds, $this->getColumn($users));
        ScheduleUser::whereIn('user_id', $deleteUsers)->where('schedule_id', $schedule->id)->delete();

        $contacts = $schedule->contacts;
        $deleteContacts = array_intersect($contactIds, $this->getColumn($contacts));
        ScheduleContact::whereIn('contact_id', $deleteContacts)->where('schedule_id', $schedule->id)->delete();

        return true;
    }

    public function invite($schedule, $emails, $user) {
        $scheduleType = $schedule->classification === ScheduleClassification::MEETING ? "a meeting" : "an action";
        $content = View::make('emails.schedule-template', [
            'from' => $this->customDate($schedule->started_at), 
            'to' => $this->customDate($schedule->finished_at), 
            'online' => $schedule->type === ScheduleType::ONLINE, 
            'place' => $schedule->place, 
            'type' => $scheduleType,
            "title" => $schedule->title
        ])->render();

        $subject = "Invite to join " . $scheduleType;

        $type = "Cc: " . implode(', ', $emails);

        SendMailFromUser::dispatch($type, $subject, $content, $user);
    }

    public function createSchedule($schedule, $emails)
    {
        $user = auth()->user();
        $service = $this->gmailTokenService->getCalendarService($user);
        $newEmail = [];
        foreach($emails as $email) {
            $newEmail[] = [
                'email'=> $email
            ];
        }

        $autoCreateMeeting = $schedule->type == ScheduleType::ONLINE and ($schedule->place == '' or $schedule->place == null);

        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('email');
        $reminder->setMinutes(30);

        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides([$reminder]);

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
        ]);

        $event->setReminders($reminders);

        if ($autoCreateMeeting) {
            $conference = new Google_Service_Calendar_ConferenceData();
            $conferenceRequest = new Google_Service_Calendar_CreateConferenceRequest();
            $conferenceSolutionKey = new Google_Service_Calendar_ConferenceSolutionKey();
            $conferenceSolutionKey->setType("hangoutsMeet");
            $conferenceRequest->setRequestId($user->email . time());
            $conferenceRequest->setConferenceSolutionKey($conferenceSolutionKey);
            $conference->setCreateRequest($conferenceRequest);
            $event->setConferenceData($conference);
        }

        $calendarId = 'primary';

        try {
            $createEvent = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));
            if($autoCreateMeeting) {
                $meetingLink = $createEvent->getHangoutLink();
                $schedule->place = $meetingLink;
                $schedule->save();
            }
            
        } catch (Google_Service_Exception $e) {
            return $e->getMessage();
        }
    }

    
}