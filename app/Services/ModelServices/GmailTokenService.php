<?php

namespace App\Services\ModelServices;
use App\Models\GmailToken;
use App\Models\User;
use Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google_Service_Gmail;
use Http;


class GmailTokenService extends BaseService
{
    public function __construct(GmailToken $gmailToken)
    {
        $this->model = $gmailToken;
    }

    private function getAccessToken($user)
    {
        if (is_numeric($user)) {
            $user = User::where('id', $user)->first();
        }

        $accessToken = $user->gmailToken->access_token;
        $expiredAt = $user->gmailToken->expired_at;

        if (now() >= $expiredAt) {
            $newAccessToken = $this->refreshAccessToken($user->gmailToken->refresh_token);

            $user->gmailToken->update([
                'access_token' => $newAccessToken['access_token'],
                'expired_at' => now()->addSeconds($newAccessToken['expires_in']),
            ]);

            return $newAccessToken['access_token'];
        }

        return $accessToken;
    }

    private function refreshAccessToken($refreshToken)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        $responseData = $response->json();

        return [
            'access_token' => $responseData['access_token'],
            'expires_in' => $responseData['expires_in'],
        ];
    }

    public function getEmailInforFromToken($token)
    {
        list($header, $payload, $signature) = explode('.', $token);

        $decodedPayload = json_decode(base64_decode($payload), true);

        return $decodedPayload;
    }

    public function getGmailService($user) {
        $client = new Google_Client();
        $client->setAccessToken($this->getAccessToken($user));
        $service = new Google_Service_Gmail($client);
        return $service;
    }

    public function getCalendarService($user)
    {
        $client = new Google_Client();
        $client->setAccessToken($this->getAccessToken($user));
        $service = new Google_Service_Calendar($client);
        return $service;
    }

    public function sendMail($type, $subject, $content, $user)
    {
        $service = $this->getGmailService($user);
        $boundary = uniqid(rand(), true);

        $rawMessage =
            "From: " . $user->email . "\r\n" .
            $type . "\r\n" .
            "Subject: $subject\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n" .
            "--$boundary\r\n" .
            "Content-Type: text/html; charset=UTF-8\r\n" .
            "Content-Transfer-Encoding: 7bit\r\n\r\n" .
            $content . "\r\n" .
            "--$boundary--";

        $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

        try {
            $service->users_messages->send('me', new \Google_Service_Gmail_Message(['raw' => $encodedMessage]));
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}