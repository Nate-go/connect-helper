<?php

namespace App\Services\ModelServices;
use App\Models\GmailToken;
use App\Models\User;
use Google_Client;
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
        $expiresedAt = $user->gmailToken->expiresed_at;

        if (now() >= $expiresedAt) {
            $newAccessToken = $this->refreshAccessToken($user->gmailToken->refresh_token);

            $user->gmailToken->update([
                'access_token' => $newAccessToken['access_token'],
                'expiresed_at' => now()->addSeconds($newAccessToken['expires_in']),
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
}