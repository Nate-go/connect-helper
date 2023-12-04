<?php

namespace App\Services\ModelServices;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Http\Resources\EmployeesResource;
use App\Jobs\SendMailFromUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class UserService extends BaseService
{
    protected $gmailTokenService;

    public function __construct(User $user, GmailTokenService $gmailTokenService)
    {
        $this->model = $user;
        $this->gmailTokenService = $gmailTokenService;
    }

    public function getEnterpriseEmployee($user, $input)
    {
        $search = $input['search'] ?? '';

        $query = $this->model->userCoworkers($user)->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']);
        $data = $this->getAll($input, $query);
        $data['items'] = EmployeesResource::collection($data['items']);

        return $data;
    }

    public function getAllOwner()
    {
        $owners = User::whereNot('role', UserRole::ADMIN)->get();

        return $owners;
    }

    public function isEmailExist($email)
    {
        return User::where('email', $email)->where('status', UserStatus::ACTIVE)->exists();
    }

    public function getCoworkers($user)
    {
        return $user->coworkers;
    }

    public function invites($emails)
    {
        $user = auth()->user();

        $data = [
            'enterprise' => $user->enterprise,
            'expired_at' => Carbon::now()->addDays(3),
        ];

        $link = env('SIGNUP_LINK');
        $link = str_replace('%FE_APP_URL%', env('FE_APP_URL'), $link);
        $link = str_replace('%INVITE_TOKEN%', $this->encryptToken($data), $link);

        $content = View::make('emails.send-mail-invite-template', ['enterpriseName' => $user->enterprise->name, 'link' => $link])->render();

        $subject = 'Invite to be an employee of '.$user->enterprise->name.' via ConnectionHelper';

        $type = 'Bcc: '.implode(', ', $emails);

        $this->addMailToQueue($type, $subject, $content, $user);

        return true;
    }
}
