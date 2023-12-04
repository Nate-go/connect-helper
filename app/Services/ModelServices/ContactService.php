<?php

namespace App\Services\ModelServices;

use App\Models\Contact;

class ContactService extends BaseService
{
    public function __construct(Contact $contact)
    {
        $this->model = $contact;
    }

    public function delete($id)
    {
        $contact = $this->model->where('id', $id)->first();

        if (! $contact) {
            return false;
        }

        $contact->deleteHistories();
        $contact->delete();

        return true;
    }
}
