<?php 

namespace App\Services\ModelServices;
use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Http\Resources\EmployeesResource;
use App\Jobs\SendMailFromUser;
use App\Jobs\SetupDataForUser;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use App\Models\User;
use Carbon\Carbon;

class UserService extends BaseService {
    protected $gmailTokenService;

    public function __construct(User $user, GmailTokenService $gmailTokenService)
    {
        $this->model = $user;
        $this->gmailTokenService = $gmailTokenService;
    }

    public function getEnterpriseEmployee($user, $input) {
        $search = $input['search'] ?? '';

        $query = $this->model->userCoworkers($user)->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        $data = $this->getAll($input, $query);
        $data['items'] = EmployeesResource::collection($data['items']);
        return $data;
    }

    public function getAllOwner() {
        $owners = User::whereNot('role', UserRole::ADMIN)->get();
        return $owners;
    }

    public function isEmailExist($email) {
        return User::where('email', $email)->where('status', UserStatus::ACTIVE)->exists();
    }

    public function getCoworkers($user) {
        return $user->coworkers;
    }

    public function invites($emails) {
        $user = auth()->user();

        $data = [
            'enterprise' => $user->enterprise,
            'expired_at' => Carbon::now()->addDays(3)
        ];
        
        $link = env('SIGNUP_LINK');
        $link = str_replace("%FE_APP_URL%", env("FE_APP_URL") , $link);
        $link = str_replace("%INVITE_TOKEN%", $this->encryptToken($data), $link);

        $content = View::make('emails.send-mail-invite-template', ['enterpriseName' => $user->enterprise->name, 'link' => $link])->render();

        $subject = "Invite to be an employee of " . $user->enterprise->name . " via ConnectionHelper";

        $type = "Bcc: " . implode(', ', $emails);

        SendMailFromUser::dispatch($type, $subject, $content, $user);

        return true;
    }

    public function getDashboard($user, $year) {
        return $this->getConnectionsByMonth($user, $year);
    }

    public function getConnectionsByMonth($user, $year)
    {
        $connections = $user->connections()
            ->whereYear('created_at', $year)->get();

        $data = [
            ConnectionStatus::PUBLIC => [],
            ConnectionStatus::PRIVATE => []
        ];
        foreach($connections as $connection) {
            $month = Carbon::parse($connection->created_at)->month;
            $status = $connection->status == ConnectionStatus::COWORKER ? ConnectionStatus::PUBLIC : $connection->status;
            if(isset($data[$connection->status][$month - 1])) {
                $data[$status][$month - 1] += 1;
            } else {
                $data[$status][$month - 1] = 0;
            }
        }

        return $data;
    }
}