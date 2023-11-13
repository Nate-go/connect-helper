<?php

namespace App\Services\ModelServices;
use App\Models\Contact;

class ContactService extends BaseService
{
    public function __construct(Contact $contact) {
        $this->model = $contact;
    }


}