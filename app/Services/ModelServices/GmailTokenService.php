<?php

namespace App\Services\ModelServices;
use App\Models\GmailToken;
use App\Models\User;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_PeopleService;


class GmailTokenService extends BaseService
{
    public function __construct(GmailToken $gmailToken)
    {
        $this->model = $gmailToken;
    }
    
    private function getAccessToken($user) {
        if(is_numeric($user)) {
            $user = User::where('id', $user)->first();
        }

        return $user->gmailToken->access_token;
    }

    public function getEmailFromToken($token)
    {
        $client = new Google_Client();
        $client->setAccessToken($token);
        $service = new Google_Service_PeopleService($client);

        $person = $service->people->get('people/me', ['personFields' => 'emailAddresses']);
        return $person->getEmailAddresses()[0]->getValue();
    }

    public function getGmailService($user) {
        $client = new Google_Client();
        $client->setAccessToken($this->getAccessToken($user));
        $service = new Google_Service_Gmail($client);
    
        return $service;
    }
}