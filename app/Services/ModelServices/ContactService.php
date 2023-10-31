<?php

namespace App\Services\ModelServices;
use App\Models\Contact;

class ContactService extends BaseService
{
    public function __construct() {
        parent::__construct(Contact::class);
    }
}