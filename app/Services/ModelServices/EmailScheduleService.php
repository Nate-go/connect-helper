<?php

namespace App\Services\ModelServices;

use App\Constants\EmailScheduleConstant\EmailScheduleStatus;
use App\Constants\SendMailConstant\SendMailType;
use App\Http\Resources\EmailScheduleResource;
use App\Models\EmailSchedule;
use Carbon\Carbon;

class EmailScheduleService extends BaseService
{
    protected $sendMailContactService;

    protected $gmailTokenService;

    public function __construct(EmailSchedule $emailSchedule, SendMailContactService $sendMailContactService, GmailTokenService $gmailTokenService)
    {
        $this->model = $emailSchedule;
        $this->sendMailContactService = $sendMailContactService;
        $this->gmailTokenService = $gmailTokenService;
    }

    public function get($input)
    {
        $search = $input['search'] ?? '';
        $user = auth()->user();
        $query = $this->model->with(['sendMail' => ['contacts']])->where('user_id', $user->id)->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']);
        $data = $this->getAll($input, $query);
        $data['items'] = EmailScheduleResource::collection($data['items']);

        return $data;
    }

    public function show($id)
    {
        $emailSchedule = $this->model->where('id', $id)->first();

        if (! $emailSchedule) {
            return false;
        }

        return $emailSchedule;
    }

    public function run()
    {
        $mailSchedules = $this->model->where('status', EmailScheduleStatus::RUNNING)->where('nextTime_at', '<=', now())->get();

        foreach ($mailSchedules as $mailSchedule) {
            if ($mailSchedule->after_second !== 0) {
                $currentTime = $mailSchedule->nextTime_at;
                $afterSecond = $mailSchedule->after_second;
                do {
                    $currentTime = $this->addSecond($currentTime, $afterSecond);
                } while (Carbon::parse($currentTime) <= now());
                $mailSchedule->nextTime_at = $currentTime;
                $mailSchedule->save();
            } else {
                $mailSchedule->status = EmailScheduleStatus::PAUSE;
                $mailSchedule->save();
            }

            $this->sendMail($mailSchedule->sendMail);
        }

        return true;
    }

    public function sendMail($sendMail)
    {
        if (! $sendMail) {
            return false;
        }

        $user = $sendMail->user;

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
        $this->gmailTokenService->sendMail($type.implode(', ', $emails), $subject, $content, $user);

        return true;
    }
}
