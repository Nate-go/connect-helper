<?php

namespace App\Services\ModelServices;

use App\Constants\EmailScheduleConstant\EmailScheduleStatus;
use App\Constants\SendMailConstant\SendMailType;
use App\Jobs\SendMailFromUser;
use App\Models\EmailSchedule;
use Illuminate\Support\Facades\Log;


class EmailScheduleService extends BaseService
{
    protected $sendMailContactService;

    public function __construct(EmailSchedule $emailSchedule, SendMailContactService $sendMailContactService)
    {
        $this->model = $emailSchedule;
        $this->sendMailContactService = $sendMailContactService;
    }

    public function get($input) {
        $search = $input['search'] ?? '';
        $user = auth()->user();
        $query = $this->model->with(['sendMail' => ['contacts']])->where('user_id', $user->id)->whereNot('after_second', 0)->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        $data = $this->getAll($input, $query);
        $data['items'] = EmployeesResource::collection($data['items']);

        return $data;
    }

    public function run() {
        $mailSchedules = $this->model->where('status', EmailScheduleStatus::RUNNING)->where('nextTime_at', '<=', now())->get();

        foreach($mailSchedules as $mailSchedule) {
            if($mailSchedule->after_second !== 0) {
                $mailSchedule->nextTime_at = $this->addSecond($mailSchedule->nextTime_at, $mailSchedule->after_second);
                $mailSchedule->save();
            } else {
                $mailSchedule->status = EmailScheduleStatus::PAUSE;
                $mailSchedule->save();
            }
            $this->testSendMail($mailSchedule);
        }
    }

    public function testSendMail($sendMail)
    {
        Log::info($sendMail);
    }
    public function sendMail($sendMail)
    {
        $user = $sendMail->user;

        if (!$sendMail) {
            return false;
        }

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

        SendMailFromUser::dispatch($type . implode(', ', $emails), $subject, $content, $user);

        return true;
    }

}
