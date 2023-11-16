<?php 

namespace App\Services\ModelServices;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
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

    public function getAllOwner() {
        $owners = User::whereNot('role', UserRole::ADMIN)->get();
        return $owners;
    }

    public function isEmailExist($email) {
        return User::where('email', $email)->where('status', UserStatus::ACTIVE)->exists();
    }

    public function getCoworkers($user) {
        try {
            $enterprise = $user->enterprise;

            return $enterprise->users;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function invites($emails) {
        $user = auth()->user();

        $data = [
            'enterprise' => $user->enterprise,
            'expired_at' => Carbon::now()->addDays(3)
        ];

        $link = "https://accounts.google.com/o/oauth2/auth?client_id=359676249009-34tqpm71tj75n1t21ibcl7u2nr1kmsn3.apps.googleusercontent.com&redirect_uri="
            . env("FE_APP_URL") .
            "&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fgmail.send%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.login%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fgmail.readonly&response_type=code&prompt=select_account&access_type=offline&state="
            . $this->encryptToken($data);

        $service = $this->gmailTokenService->getGmailService($user);

        $view = View::make('emails.send-mail-invite-template', ['enterpriseName' => $user->enterprise->name, 'link' => $link])->render();

        $service = $this->gmailTokenService->getGmailService($user);

        $subject = "Invite to be an employee of " . $user->enterprise->name . " via ConnectionHelper";

        $boundary = uniqid(rand(), true);

        $rawMessage =
            "From: " . $user->email . "\r\n" .
            "To: " . implode(', ', $emails) . "\r\n" .
            "Subject: $subject\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n" .
            "--$boundary\r\n" .
            "Content-Type: text/html; charset=UTF-8\r\n" .
            "Content-Transfer-Encoding: 7bit\r\n\r\n" .
            $view . "\r\n" .
            "--$boundary--";

        $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
        
        try {
            $service->users_messages->send('me', new \Google_Service_Gmail_Message(['raw' => $encodedMessage]));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}