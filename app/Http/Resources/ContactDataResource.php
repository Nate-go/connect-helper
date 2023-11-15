<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'contacts' => $this->contacts,
            'histories' => $this->histories->map(function ($history) {
                return [
                    'id' => $history->id,
                    'contacted_at' => $history->contacted_at,
                    'contact' => $history->contact,
                    'type' => $history->type,
                    'user' => [
                            'id' => $history->user->id,
                            'name' => $history->user->name,
                            'image_url' => $history->user->image_url,
                            'email' => $history->user->email
                        ]
                ];
            })
        ];
    }
}
