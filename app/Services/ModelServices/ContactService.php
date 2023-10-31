<?php

namespace App\Services\ModelServices;
use App\Models\Contact;

class ContactService extends BaseService
{
    public function create($data)
    {
        return Contact::create($data);
    }
}